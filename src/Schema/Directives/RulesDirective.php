<?php

namespace Nuwave\Lighthouse\Schema\Directives;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Nuwave\Lighthouse\Support\Contracts\ArgValidationDirective;
use Nuwave\Lighthouse\Support\Traits\HasArgumentPath as HasArgumentPathTrait;
use Nuwave\Lighthouse\Support\Contracts\HasArgumentPath as HasArgumentPathContract;

class RulesDirective extends BaseDirective implements ArgValidationDirective, HasArgumentPathContract
{
    use HasArgumentPathTrait;

    /**
     * Name of the directive.
     *
     * @return string
     */
    public function name(): string
    {
        return 'rules';
    }

    /**
     * @return mixed[]
     */
    public function getRules(): array
    {
        $rules = $this->directiveArgValue('apply');

        // Resolve custom rule namespace, if possible.
        foreach ($rules as $key => $rule) {

            if (strpos($rule, 'App\Rules') === 0) {
                $rules[$key] = App::make($rule);
            }
        }

        return [$this->argumentPathAsDotNotation() => $rules];
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return (new Collection($this->directiveArgValue('messages')))
            ->mapWithKeys(function (string $message, string $rule): array {
                $argumentPath = $this->argumentPathAsDotNotation();

                return ["{$argumentPath}.{$rule}" => $message];
            })
            ->all();
    }
}
