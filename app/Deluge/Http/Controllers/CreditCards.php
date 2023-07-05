<?php

namespace App\Deluge\Http\Controllers;

use App\Deluge\Http\Requests\CreditCards\Create;
use App\Deluge\Http\Requests\CreditCards\Update;
use App\Http\Controllers\Controller;
use App\ManualCreditCard;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CreditCards extends Controller
{
    /**
     * Bundles constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(ManualCreditCard::class, 'credit_card');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('deluge::credit-cards.index', [
            'creditCards' => ManualCreditCard::query()
                ->notEmptyWhereIn('buyer_id', Arr::wrap($request->input('buyer')))
                ->with(['account'])
                ->orderByDesc('created_at')
                ->paginate(),
            'buyers'      => User::visible()->withFacebookTraffic()->get(['id', 'name']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        return view('deluge::credit-cards.create', [
            'buyers' => User::visible()->withFacebookTraffic()->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Create $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Create $request)
    {
        ManualCreditCard::create($request->validated());

        return redirect()->route('deluge.credit-cards.index')
            ->with('success', 'Карта создана.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\ManualCreditCard $creditCard
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(ManualCreditCard $creditCard)
    {
        return view('deluge::credit-cards.show')->with([
            'creditCard' => $creditCard,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\ManualCreditCard $creditCard
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(ManualCreditCard $creditCard)
    {
        return view('deluge::credit-cards.edit')->with([
            'creditCard' => $creditCard,
            'buyers'     => User::visible()->withFacebookTraffic()->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Update                $request
     * @param \App\ManualCreditCard $creditCard
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Update $request, ManualCreditCard $creditCard)
    {
        $creditCard->update($request->validated());

        return redirect()->route('deluge.credit-cards.show', $creditCard);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\ManualCreditCard $creditCard
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy(ManualCreditCard $creditCard)
    {
        $creditCard->delete();

        return redirect()->route('deluge.credit-cards.index')
            ->with('message', 'Карта успешно удалена.');
    }
}
