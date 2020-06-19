<?php

namespace App\Services\Shared;

abstract class TransformerService {

  public function transformCollection($items){
    $data = [];

    foreach ($items as $item) {
      $payload = $this->transform($item);
      if ($payload) {
        $data[] = $payload;
      }
    }

    return $data;
  }

  public abstract function transform($item);
}
