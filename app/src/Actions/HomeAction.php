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
        $methods = [];

        $versions = array_diff(scandir(self::CONTRACTS_PATH), ['..', '.']);
        foreach ($versions as $version) {
            $extensions = array_diff(scandir(self::CONTRACTS_PATH . '//' . $version), ['..', '.']);

            foreach ($extensions as $extension) {
                $contracts = array_diff(scandir(self::CONTRACTS_PATH . '//' . $version . '//' . $extension), ['..', '.']);

                foreach ($contracts as $contract) {
                    $data = file_get_contents(self::CONTRACTS_PATH . '//' . $version . '//' . $extension . '//' . $contract);
                    $data = json_decode($data, true);

                    $methods[$version][ucfirst($extension)][ucfirst(str_replace(".json", "", $contract))] = $data;

                    $example = $methods[$version][ucfirst($extension)][ucfirst(str_replace(".json", "", $contract))]['success']['request']['example'];
                    $example = json_encode($example);

                    $methods[$version][ucfirst($extension)][ucfirst(str_replace(".json", "", $contract))]['success']['request']['example'] = $example;
                }
            }
        }

        $this->view->render($response, 'index.twig', [
            'v1' => $methods['v1'],
            'v2' => $methods['v2'],
        ]);

        return $response;
    }
}
