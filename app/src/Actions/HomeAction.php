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
        $versions       = array_diff(scandir(self::CONTRACTS_PATH), ['..', '.']);
        $default        = current($versions);
        $currentVersion = $request->getParam('version', $default);
        foreach ($versions as $version) {
            $extensions = array_diff(scandir(self::CONTRACTS_PATH . '//' . $version), ['..', '.']);

            foreach ($extensions as $extension) {
                $contracts = array_diff(scandir(self::CONTRACTS_PATH . '//' . $version . '//' . $extension), ['..', '.']);

                foreach ($contracts as $contract) {
                    $data = file_get_contents(self::CONTRACTS_PATH . '//' . $version . '//' . $extension . '//' . $contract);
                    $data = json_decode($data, true);

                    $contract = str_replace(".json", "", $contract);

                    $methods[$version][$extension][$contract] = $data;

                    $ReqEx = $methods[$version][$extension][$contract]['success']['request']['example'];
                    $ReqEx = [
                        'jsonrpc' => '2.0',
                        'method'  => sprintf('%s.%s', $extension, $contract),
                        'params'  => $ReqEx,
                        'id'      => 1,
                    ];

                    $ResEx = $methods[$version][$extension][$contract]['success']['response']['example'];
                    $ResEx = [
                        'jsonrpc' => '2.0',
                        'result'  => $ResEx,
                        'id'      => 1,
                    ];

                    $methods[$version][$extension][$contract]['success']['request']['example'] = $ReqEx;
                    $methods[$version][$extension][$contract]['success']['response']['example'] = $ResEx;
                }
            }
        }

        $this->view->render($response, 'index.twig', [
            'docs'           => $methods[$currentVersion],
            'currentVersion' => $currentVersion,
            'otherVersions'  => array_diff($versions, [$currentVersion]),
        ]);

        return $response;
    }
}
