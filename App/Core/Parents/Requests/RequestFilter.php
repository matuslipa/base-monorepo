<?php

declare(strict_types=1);

namespace App\Core\Parents\Requests;

use Illuminate\Http\Request;

abstract class RequestFilter
{
    /**
     * Get attribute from input / model.
     *
     * @param string $field
     * @param \Illuminate\Http\Request $request
     * @param callable $fallback
     *
     * @return mixed
     */
    protected function getFieldValue(string $field, Request $request, callable $fallback): mixed
    {
        if ($request->isMethod('PATCH') && ! $request->has($field)) {
            return $fallback();
        }

        return $request->post($field);
    }

    /**
     * For PATCH method returns 'sometimes', otherwise 'required'.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function getSometimesRequiredRule(Request $request): string
    {
        return $request->isMethod('PATCH') ? 'sometimes' : 'required';
    }

    /**
     * For PATCH method returns 'sometimes', otherwise 'present'.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function getSometimesPresentRule(Request $request): string
    {
        return $request->isMethod('PATCH') ? 'sometimes' : 'present';
    }
}
