<?php
namespace App\Actions;

use Slim\Views\Twig;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

final class HomeAction
{
    const CONTRACTS_PATH = __DIR__ . '/../../../app/contracts';

    private $view;
    private $logger;

    public function __construct(Twig $view, LoggerInterface $logger)
    {
        $this->view = $view;
        $this->logger = $logger;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $methods        = [];
        $entities       = [];
        $versions       = array_diff(scandir(self::CONTRACTS_PATH), ['..', '.']);
        $default        = current($versions);
        $currentVersion = $request->getParam('version', $default);

        // Scan entities
        $entityFiles = array_diff(scandir(self::CONTRACTS_PATH . '/' . $currentVersion . '/_entities'), ['..', '.']);
        foreach ($entityFiles as $entity) {
            $data = file_get_contents(self::CONTRACTS_PATH . '/' . $currentVersion . '/_entities/' . $entity);
            $data = json_decode($data, true);

            $entity = str_replace(".json", "", $entity);

            $data['example'] = [
                'jsonrpc' => '2.0',
                'result' => $data['example'],
                'id' => 1,
            ];

            $entities[$entity] = $data;
        }

        //Scan endpoints
        $extensions = array_diff(scandir(self::CONTRACTS_PATH . '/' . $currentVersion), ['..', '.', '_entities']);
        foreach ($extensions as $extension) {
            $contracts = array_diff(scandir(self::CONTRACTS_PATH . '/' . $currentVersion . '/' . $extension), ['..', '.']);
            foreach ($contracts as $contract) {
                $data = file_get_contents(self::CONTRACTS_PATH . '/' . $currentVersion . '/' . $extension . '/' . $contract);
                $data = json_decode($data, true);

                $contract = str_replace(".json", "", $contract);

                $methods[$extension][$contract] = $data;

                if (isset($methods[$extension][$contract]['success']['request']['example'])) {
                    $reqExample = [
                        'jsonrpc' => '2.0',
                        'method' => sprintf('%s.%s', $extension, $contract),
                        'params' => $methods[$extension][$contract]['success']['request']['example'],
                        'id' => 1,
                    ];

                    $methods[$extension][$contract]['success']['request']['example'] = $reqExample;
                } elseif (isset($methods[$extension][$contract]['success']['request'])) {
                    $entityName = str_replace(".json", "", $methods[$extension][$contract]['success']['request']);
                    if (isset($entities[$entityName])) {
                        $methods[$extension][$contract]['success']['request'] = [
                            'params' => $entities[$entityName]['params'] ?? [],
                            'example'=> $entities[$entityName]['example'] ?? [],
                        ];
                    }
                }

                if (isset($methods[$extension][$contract]['success']['response']['example'])) {
                    $resExample = [
                        'jsonrpc' => '2.0',
                        'result' => $methods[$extension][$contract]['success']['response']['example'],
                        'id' => 1,
                    ];

                    $methods[$extension][$contract]['success']['response']['example'] = $resExample;
                } elseif (isset($methods[$extension][$contract]['success']['response'])) {
                    $entityName = str_replace(".json", "", $methods[$extension][$contract]['success']['response']);
                    if (isset($entities[$entityName])) {
                        $methods[$extension][$contract]['success']['response'] = [
                            'params' => $entities[$entityName]['params'] ?? [],
                            'example'=> $entities[$entityName]['example'] ?? [],
                        ];
                    }
                }
            }
        }

        $this->view->render($response, 'index.twig', [
            'docs'           => $methods,
            'currentVersion' => $currentVersion,
            'otherVersions'  => array_diff($versions, [$currentVersion]),
            'apiUrl'         => '/api/' . $currentVersion,
            'entities'       => $entities,
        ]);

        return $response;
    }
}
