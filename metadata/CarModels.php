<?php

class CarModels
{
	private $data;

	public function __construct()
	{
		$this->data = json_decode(file_get_contents(__DIR__ . '/carModels.json'), true);
	}

	public function onEach(callable $callback)
	{
		foreach ($this->data as $make => $i) {
			foreach ($i as $model => $info) {
				$callback($make, $model, $info);
			}
		}
	}

	public function getAll()
	{
		foreach ($this->data as $make => $i) {
			foreach ($i as $model => $info) {
				yield [$make, $model, $info];
			}
		}
	}

    public function getAllMakes()
    {
		foreach ($this->data as $make => $i) {
			yield $make;
		}
    }

	public function getInfo($carMake, $carModel)
	{
		return $this->data[$carMake][$carModel];
	}
}
