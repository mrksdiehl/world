<?php

namespace Nnjeim\World\Http\Controllers\Traits;

use Nnjeim\World\Actions\ActionInterface;
use Illuminate\Http\JsonResponse;

trait ResponseBuilder
{
	protected ActionInterface $action;
	protected array $response = [
		'success' => true,
		'message' => '',
		'data' => [],
	];
	protected int $statusCode = 200;

	/**
	 * @param  ActionInterface  $action
	 * @return JsonResponse
	 */
	protected function formResponse(ActionInterface $action): JsonResponse
	{
		$this
			->setAction($action)
			->setSuccess()
			->setMessage()
			->setData()
			->setErrors()
			->setMeta([
				'response_time' => 1000 * number_format((microtime(true) - LARAVEL_START), 2) . ' ms'
			])
			->setStatusCode();

		return response()->json($this->response, $this->statusCode);
	}

	/**
	 * @param  ActionInterface  $action
	 * @return $this
	 */
	protected function setAction(ActionInterface $action): self
	{
		$this->action = $action;

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function setStatusCode(): self
	{
		$this->statusCode = $this->action->statusCode ?? ($this->action->success ? 200 : 422);

		return $this;
	}

	/**
	 * @param  array  $meta
	 * @return $this
	 */
	protected function setMeta(array $meta): self
	{
		$this->response += $meta;

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function setErrors(): self
	{
		if (isset($this->action->errors) && !empty($this->action->errors)) {
			$this->response = array_merge(
				$this->response,
				['errors' => $this->action->errors]
			);
		}

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function setData(): self
	{
		$this->response['data'] = $this->action->data;

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function setMessage(): self
	{
		$this->response['message'] = $this->action->message;

		return $this;
	}

	/**
	 * @return $this
	 */
	protected function setSuccess(): self
	{
		$this->response['success'] = $this->action->success;

		return $this;
	}
}
