<?php

namespace SgateShipFromStore\Framework\Controller\Api;

use Shopware\Components\Api\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class ApiController extends \Shopware_Controllers_Api_Rest
{
    public function dispatch($action)
    {
        try {
            parent::dispatch($action);
        } catch (ValidationException $exception) {
            $errors = [];

            foreach ($exception->getViolations() as $violation) {
                $propertyPath = trim(str_replace(['[', ']'], '/', $violation->getPropertyPath()), '/');

                $errors[] = [
                    'code' => $violation->getCode(),
                    'propertyPath' => $propertyPath,
                    'message' => $violation->getMessage(),
                ];
            }

            $this->createErrorResponse($this->Response(), Response::HTTP_BAD_REQUEST, $errors);
        } catch (HttpException $exception) {
            $this->createErrorResponse($this->Response(), $exception->getStatusCode(), [$exception->getMessage()]);
        } catch (\Throwable $exception) {
            $this->createErrorResponse($this->Response(), Response::HTTP_INTERNAL_SERVER_ERROR, [$exception->getMessage()]);
        }
    }

    public function postDispatch()
    {
        $data = $this->View()->getAssign();

        if (empty($data)) {
            $this->Response()->setStatusCode(Response::HTTP_NO_CONTENT);

            return;
        }

        parent::postDispatch();
    }

    protected function createErrorResponse(Response $response, int $statusCode, array $errors): void
    {
        $response->setStatusCode($statusCode);

        $response->headers->set('content-type', 'application/json', true);
        $response->setContent(json_encode(['errors' => $errors], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}
