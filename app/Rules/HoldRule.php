<?php

namespace App\Rules;

use App\Models\Hold;
use App\Repositories\Interfaces\HoldInterface;
use App\Traits\LoggerTrait;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class HoldRule implements ValidationRule
{
    use LoggerTrait;
    private $hold;
    private HoldInterface $holdRepository;
    public function __construct(HoldInterface $holdRepository)
    {
        $this->holdRepository  = $holdRepository;
    }


    /**
     * Run the validation rule.
     *
     * @param  Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var \App\Models\User */
        $user = request()->user();

        $this->hold = $user->holds()->with("product")->where("id", $value)->first();
        if (!$this->hold) {
            $fail('Hold is not available');
            return;
        }
        if (!$this->hold->isAvailable()) {
            $this->holdRepository->release($this->hold);
            $fail('Hold is not available');
        }

        if (!$this->hold) {
            $fail('Hold is not available');
        }
    }


    public function getHold()
    {
        return $this->hold;
    }
}
