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
        $request->validate([
            'options' => 'required|array|min:1',
            'options.*.option_name' => [
                'required',
                Rule::unique('variant_options')
                    ->where(fn($q) => $q->where('variant_type_id', $variantType->id)),
            ],
            'options.*.price_impact' => 'required|numeric|min:0',
        ]);

        foreach ($request->options as $opt) {
            $variantType->options()->create([
                'option_name'  => $opt['option_name'],
                'price_impact' => $opt['price_impact'],
            ]);
        }

        return redirect()
            ->route('variant-types.options.index', $variantType)
            ->with('success', 'Option berhasil ditambahkan');
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
                    ->where(fn($q) => $q->where('variant_type_id', $variantType->id))
                    ->ignore($option->id),
            ],
            'price_impact' => 'required|numeric|min:0',
        ]);

        $option->update($data);

        return redirect()
            ->route('variant-types.options.index', $variantType)
            ->with('success', 'Option berhasil diperbarui');
    }

    public function destroy(VariantType $variantType, VariantOption $option)
    {
        $option->delete();

        return redirect()
            ->route('variant-types.options.index', $variantType)
            ->with('success', 'Option berhasil dihapus');
    }
}
