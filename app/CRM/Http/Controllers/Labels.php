<?php

namespace App\CRM\Http\Controllers;

use App\CRM\Http\Requests\CreateLabel;
use App\CRM\Http\Requests\UpdateLabel;
use App\CRM\Label;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class Labels extends Controller
{
    /**
     * Labels constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Label::class, 'label');
    }
    /**
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator
     */
    public function index()
    {
        $labels = Label::query()
            ->orderByDesc('id')
            ->get();

        return view('crm::labels.index', [
            'labels' => $labels
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('crm::labels.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateLabel $request
     *
     * @return Response
     */
    public function store(CreateLabel $request)
    {
        Label::create($request->validated());

        return redirect()->route('crm.labels.index')
            ->with('success', 'Label created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param Label $label
     *
     * @return Response
     */
    public function show(Label $label)
    {
        return view('crm::labels.show')->with([
            'label' => $label,
        ]);
    }

    /**
     * Display editing form for label
     *
     * @param Label $label
     *
     * @return Application|Factory|View
     */
    public function edit(Label $label)
    {
        return view('crm::labels.edit')->with([
            'label' => $label
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateLabel $request
     * @param Label       $label
     *
     * @return RedirectResponse
     */
    public function update(UpdateLabel $request, Label $label)
    {
        $label->update($request->validated());

        return redirect()->route('crm.labels.index')
            ->with('success', 'Label updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Label $label
     *
     * @throws Exception
     * @throws AuthorizationException
     *
     * @return RedirectResponse
     *
     */
    public function destroy(Label $label)
    {
        $label->delete();

        return redirect()->route('crm.labels.index')
            ->with('success', 'Label deleted successfully');
    }
}
