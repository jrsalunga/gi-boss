<?php namespace App\Repositories;

use Exception;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;
use App\Traits\Repository as RepoTrait;

class FiletypeRepository extends BaseRepository implements CacheableInterface
{
  use CacheableRepository, RepoTrait;

  protected $order = ['code', 'descriptor'];
  
  public function model() {
    return 'App\\Models\\Filetype';
  }
}