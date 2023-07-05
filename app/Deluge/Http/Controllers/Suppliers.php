<?php

namespace App\Deluge\Http\Controllers;

use App\Branch;
use App\Deluge\Http\Requests\Suppliers\Create;
use App\Deluge\Http\Requests\Suppliers\Update;
use App\Http\Controllers\Controller;
use App\ManualSupplier;
use Illuminate\Http\Request;

class Suppliers extends Controller
{
    /**
     * Suppliers constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(ManualSupplier::class, 'supplier');
    }

    /**
     * Index suppliers
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View|void
     */
    public function index(Request $request)
    {
        return view('deluge::suppliers.index', [
            'suppliers' => ManualSupplier::visible()
                ->with(['branch'])
                ->orderByDesc('name')
                ->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        return view('deluge::suppliers.create', [
            'branches' => Branch::allowed()->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Deluge\Http\Requests\Suppliers\Create $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        ManualSupplier::create($request->validated());

        return redirect()->route('deluge.suppliers.index')
            ->with('success', 'Supplier was created successfully.');
    }

    /**
     * Show single supplier page
     *
     * @param \App\ManualSupplier $supplier
     *
     * @return \Illuminate\View\View
     */
    public function show(ManualSupplier $supplier, Request $request)
    {
        return view('deluge::suppliers.show')->with([
            'supplier' => $supplier->loadMissing(['branch']),
        ]);
    }

    /**
     * Display editing form for supplier
     *
     * @param \App\ManualSupplier $supplier
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(ManualSupplier $supplier)
    {
        return view('deluge::suppliers.edit')->with([
            'supplier' => $supplier,
            'branches' => Branch::allowed()->get(['id', 'name']),
        ]);
    }

    /**
     * Update supplier details
     *
     * @param \App\ManualSupplier                        $supplier
     * @param \App\Deluge\Http\Requests\Suppliers\Update $request
     *
     * @throws \Throwable
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ManualSupplier $supplier, Update $request)
    {
        $supplier->update($request->validated());

        return redirect()->route('deluge.suppliers.show', $supplier);
    }
}
