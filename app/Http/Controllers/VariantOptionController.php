<?php

namespace App\Http\Controllers;

use App\Models\VariantType;
use App\Models\VariantOption;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VariantOptionController extends Controller
{
    public function index(VariantType $variantType)
    {
        $options = $variantType->options;
        return view('admin.variant_option.index', compact('variantType', 'options'));
    }

    public function create(VariantType $variantType)
    {
        return view('admin.variant_option.create', compact('variantType'));
    }

    public function store(Request $request, VariantType $variantType)
    {
        $data = $request->validate([
            'option_name' => [
                'required',
                Rule::unique('variant_options')
                    ->where(fn ($q) => $q->where('variant_type_id', $variantType->id)),
            ],
            'price_impact' => 'required|numeric',
        ]);

        $variantType->options()->create($data);

        return redirect()->route('variant-types.options.index', $variantType);
    }

    public function edit(VariantType $variantType, VariantOption $option)
    {
        return view('admin.variant_option.edit', compact('variantType', 'option'));
    }

    public function update(Request $request, VariantType $variantType, VariantOption $option)
    {
        $data = $request->validate([
            'option_name' => [
                'required',
                Rule::unique('variant_options')
                    ->where(fn ($q) => $q->where('variant_type_id', $variantType->id))
                    ->ignore($option->id),
            ],
            'price_impact' => 'required|numeric',
        ]);

        $option->update($data);

        return redirect()->route('variant-types.options.index', $variantType);
    }

    public function destroy(VariantType $variantType, VariantOption $option)
    {
        $option->delete();

        return redirect()->route('variant-types.options.index', $variantType);
    }
}
