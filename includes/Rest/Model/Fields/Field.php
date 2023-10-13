<?php

declare(strict_types=1);

namespace TdNewsletter\Rest\Model\Fields;

use TdNewsletter\Sanitize\SanitizerInterface;
use TdNewsletter\Validate\ValidatorInterface;

class Field {
	private string $name;
	private bool $isRequired;
	private string $type;
	private ValidatorInterface $validator;
	private SanitizerInterface $sanitizer;

	public function __construct(string $name, bool $isRequired, string $type, ValidatorInterface $validator, SanitizerInterface $sanitizer) {
			$this->name = $name;
			$this->isRequired = $isRequired;
			$this->type = $type;
			$this->validator = $validator;
			$this->sanitizer = $sanitizer;
	}

	final public function asArgs(): array {
			return [
					'required' => $this->isRequired,
					'validate_callback' => [$this->validator, 'validate'],
					'sanitize_callback' => [$this->sanitizer, 'sanitize'],
					'type' => $this->type
			];
	}

	public function getName() : string {
		return $this->name;
	}
}
