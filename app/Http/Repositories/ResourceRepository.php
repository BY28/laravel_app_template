<?php

namespace App\Http\Repositories;
Use App\Http\Resources\Resource as Resource;

use File;

class ResourceRepository
{

    protected $model;

    public function get()
	{
		return $this->model->get();
	}

    public function getPaginate($n, $col, $order)
	{
		return $this->model->orderBy($col, $order)->paginate($n);
	}

	public function store(Array $inputs)
	{
		return $this->model->create($inputs);
	}

	public function insert($inputs)
	{
		return $this->model->insert($inputs);
	}

	public function getById($id)
	{
		return $this->model->findOrFail($id);
	}

	public function update($id, Array $inputs)
	{
		$this->getById($id)->update($inputs);
		return $this->getById($id);
	}

	public function destroy($id)
	{
		return $this->getById($id)->delete();
	}

	public function moveFile($file, $path)
	{
		if($file->isValid())
		{
			$name = '';

			$extension = $file->getClientOriginalExtension();

			// do {
				$name = str_random(60) . time() . '.' . $extension;
			// } while(file_exists($path . '/' . $name)); // checks if filename already exists

			if($file->move($path, $name)) 
			{
				return $name;
			}
			else
			{
				return '';
			}
		}
	}

	public function deleteFile($file, $path)
	{
		File::delete($path . $file);
	}

	public function collectionToJson($data)
	{
		return Resource::collection($data);
	}

	public function objectToJson($data)
	{
		return new Resource($data);
	}

}