<?php

class CarModels
{
	private $data;

	public function __construct()
	{
		$x = json_decode(file_get_contents(__DIR__ . '/carModels.json'), true);
		$this->data = array_map(
			function ($i) {return array_combine($i, $i);},
			$x
		);
	}

	public function onEach(callable $callback)
	{
		foreach ($this->data as $make => $i) {
			foreach ($i as $model) {
				$callback($make, $model);
			}
		}
	}

	public function getAll()
	{
		foreach ($this->data as $make => $i) {
			foreach ($i as $model) {
				yield [$make, $model];
			}
		}
	}

    public function getAllMakes()
    {
		foreach ($this->data as $make => $i) {
			yield $make;
		}
    }
}
