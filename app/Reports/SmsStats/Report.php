<?php

namespace App\Reports\SmsStats;

use App\Branch;
use App\Facebook\Account;
use App\SmsMessage;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Report implements Responsable, Arrayable
{
    /**
     * Select report for a specific date
     *
     * @var Carbon|null
     */
    protected $since;
    /**
     * Select report for a specific date
     *
     * @var Carbon|null
     */
    protected $until;

    /**
     * @var Branch|null
     */
    protected $branch;

    /**
     * Messages for report
     *
     * @var Collection|null
     */
    protected $messages;

    /**
     * Accounts for report
     *
     * @var Collection|null
     */
    protected $accounts;

    /**
     * Construct report
     *
     */
    public function __construct(array $settings = [])
    {
        $this->forPeriod($settings['since'] ?? null, $settings['until'] ?? null)
            ->forBranch($settings['branch'] ?? null)
            ->getAccounts()
            ->getMessages();
    }

    /**
     * Named constructor
     *
     * @return Report
     */
    public static function fromRequest(Request $request)
    {
        return new self([
            'since'       => $request->get('since'),
            'until'       => $request->get('until'),
            'branch'      => $request->get('branch'),
        ]);
    }

    /**
     * Get certain accounts
     *
     * @return Report
     */
    public function getAccounts()
    {
        $this->accounts = Account::query()->get();

        return  $this;
    }

    /**
     * Get messages
     *
     * @return Report
     */
    public function getMessages()
    {
        $this->messages = SmsMessage::query()
            ->whereBetween('created_at', [$this->since->startOfDay(), $this->until->endOfDay()])
            ->whereHas('campaign', fn ($query) => $query->where('branch_id', optional($this->branch)->id))
            ->get();

        return  $this;
    }

    /**
     * Filter stats by specific date
     *
     * @param null $since
     * @param null $until
     *
     * @return Report
     */
    public function forPeriod($since = null, $until = null)
    {
        $this->since = $since ? Carbon::parse($since) : now();
        $this->until = $until ? Carbon::parse($until) : now();

        return $this;
    }

    /**
     * @param int|string|null $branch
     *
     * @return Report
     */
    public function forBranch($branch)
    {
        $this->branch = Branch::find($branch ?: auth()->user()->branch_id);

        return $this;
    }

    /**
     * Get only sent messages count
     *
     * @return string
     */
    protected function getSent()
    {
        return $this->messages->count();
    }

    /**
     * Gets cost for sent messages
     *
     * @return mixed
     */
    protected function getSentCost()
    {
        return $this->messages->sum('cost');
    }

    /**
     * Get only delivered messages count
     *
     * @return string
     */
    protected function getDelivered()
    {
        return $this->messages->where('status', 'delivered')->count();
    }

    protected function getDeliveredCost()
    {
        return $this->messages->where('status', 'delivered')->sum('cost');
    }

    /**
     * {@inheritDoc}
     */
    public function toArray()
    {
        return [
            'sent'            => $this->sent(),
            'delivered'       => $this->delivered(),
            'success'         => $this->success(),
            'balance'         => $this->balance(),
            'expense'         => $this->expense(),
            'averageCost'     => $this->averageCost(),
        ];
    }

    /**
     * Get sent messages sum
     *
     * @return array
     */
    public function sent(): array
    {
        return [
            'label'   => 'Отправлено',
            'measure' => $this->getSent(),
        ];
    }

    /**
     * Get delivered messages sum
     *
     * @return array
     */
    public function delivered(): array
    {
        return [
            'label'   => 'Доставлено',
            'measure' => $this->getDelivered(),
        ];
    }

    /**
     * Get delivered-to-sent messages percent
     *
     * @return array
     */
    public function success(): array
    {
        $this->getSent() !== 0 ?
            $percent   = round($this->getDelivered() * 100 / $this->getSent(), 2)
            : $percent = 0;

        return [
            'label'   => 'Процент доставки',
            'measure' => $percent . ' %',
        ];
    }

    /**
     * Get balance for messages
     *
     * @return array
     */
    public function balance(): array
    {
        $balance = optional(optional($this->branch)->initializeSmsService())->getBalance();

        return [
            'label'   => 'Баланс',
            'measure' => (isset($balance['money']) ? round($balance['money'], 2) : 'N/A')
                . ' ' . ($balance['currency'] ?? ''),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function toResponse($request)
    {
        return response()->json($this->toArray(), 200);
    }

    /**
     * Get expense for messages
     *
     * @return array
     */
    public function expense(): array
    {
        return [
            'label'   => 'Расход',
            'measure' => sprintf('%s $', round($this->getDeliveredCost(), 2)),
        ];
    }

    /**
     * Gets an average cost
     *
     * @return array
     */
    public function averageCost(): array
    {
        return [
            'label'     => 'Средняя стоимость',
            'measure'   => $this->getSent() != 0
                ? round($this->getSentCost() / $this->getSent(), 2)
                : 'N/A',
        ];
    }
}
