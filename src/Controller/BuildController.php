<?php

namespace StackPress\Controller;

use StackPress\Service\Builder;

class BuildController {
    private Builder $builder;

    public function __construct(?Builder $builder = null) {
        $this->builder = $builder ?? new Builder();
    }

    public function build(): void {
        $this->builder->execute();
    }
}
