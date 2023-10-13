<?php
declare(strict_types=1);
namespace TdNewsletter\Rest\Model\Route;
use TdNewsletter\Rest\Model\Endpoints\Endpoint;


class Route
{
  private string $namespace;
  private string $name;
  
 /**
	* @var Endpoint[]
	*/
	private array $endpoints;


  public function __construct(string $namespace, string $name) {
    $this->namespace = $namespace;
    $this->name = $name;
  }

	/**
	 * @return string
	 */
	public function getNamespace(): string {
		return $this->namespace;
	}
	
	/**
	 * @param string $namespace 
	 * @return self
	 */
	public function setNamespace(string $namespace): self {
		$this->namespace = $namespace;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	/**
	 * @param string $name
	 * @return self
	 */
	public function setRoute(string $name): self {
		$this->name = $name;
		return $this;
	}
	
	/**
	 * @return Endpoint[]
	 */
	public function getEndpoints(): array {
		return $this->endpoints;
	}
	
	/**
	 * @param Endpoint $endpoint
	 * @return self
	 */
	public function addEndpoint(Endpoint $endpoint): self {
		$this->endpoints[] = $endpoint;
		return $this;
	}
}

