<?php

namespace App\Jobs;

use App\LeadOrderAssignment;
use App\Proxy;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Chrome\ChromeProcess;

class SimulateAutologin implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var \App\LeadOrderAssignment
     */
    public LeadOrderAssignment $assignment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LeadOrderAssignment $assignment)
    {
        $this->assignment = $assignment->fresh();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->assignment->isDelivered() && !empty($this->assignment->redirect_url)) {
            $proxy     = $this->getProxy();
            $userAgent = !empty(optional($this->assignment->lead->click)->user_agent)
                ? optional($this->assignment->lead->click)->user_agent
                : $this->randomUserAgent();

            $process = (new ChromeProcess())->toProcess();
            $process->start();
            $options = (new ChromeOptions())
                ->addArguments([
                    '--no-sandbox',
                    '--disable-gpu',
                    '--headless',
                    '--proxy-server=' . optional($proxy)->credentials_url,
                    '--user-agent=' . $userAgent,
                ]);
            $capabilities = DesiredCapabilities::chrome()
                ->setCapability(ChromeOptions::CAPABILITY, $options);

            $driver = retry(5, function () use ($capabilities) {
                return RemoteWebDriver::create(
                    'http://localhost:9515',
                    $capabilities,
                );
            }, 50);

            $browser = new Browser($driver);
            $browser->visit($this->assignment->redirect_url);

            $browser->pause(rand(4000, 7000));

            // $path = sprintf("chrome-screenshots/assignments/%s-%s.png", $this->assignment->id, now()->format('YmdHis'));
            // Storage::disk('s3')->put($path, $driver->takeScreenShot(), 'public');

            $browser->quit();
            $process->stop();
            $driver->quit();

            $this->assignment->lead->addEvent('simulation');

            // $this->assignment->lead->addEvent('simulation', [
            //     'assignment_id' => $this->assignment->id,
            //     'screenshot'    => Storage::disk('s3')->url($path),
            // ]);
            $this->assignment->update([
                'is_live' => true,
            ]);
        }
    }

    /**
     * @return Proxy|null
     */
    protected function getProxy(): ?Proxy
    {
        // by geo && lead branch
        $proxy = Proxy::active()->whereGeo(optional($this->assignment->lead->lookup)->country)
            // ->whereBranchId($this->assignment->lead->user->branch_id)
            ->orderByRaw('random()')
            ->first();

        // by geo && auth branch
        if ($proxy === null && auth()->check()) {
            $proxy = Proxy::active()->whereGeo(optional($this->assignment->lead->lookup)->country)
                // ->whereBranchId($this->assignment->lead->user->branch_id)
                ->orderByRaw('random()')
                ->first();
        }

        if ($proxy !== null) {
            $check = $proxy->checkConnection();
            if (!$check['active']) {
                $proxy->update(['is_active' => false]);

                return $this->getProxy();
            }
        }

        return $proxy;
    }

    /**
     * @return string
     */
    protected function randomUserAgent(): string
    {
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/604.3.5 (KHTML, like Gecko) Version/11.0.1 Safari/604.3.5";
        $userAgentArray[] = "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36 OPR/49.0.2725.47";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/604.4.7 (KHTML, like Gecko) Version/11.0.2 Safari/604.4.7";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:57.0) Gecko/20100101 Firefox/57.0";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36 Edge/15.15063";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:57.0) Gecko/20100101 Firefox/57.0";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36 Edge/16.16299";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/604.4.7 (KHTML, like Gecko) Version/11.0.2 Safari/604.4.7";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/604.3.5 (KHTML, like Gecko) Version/11.0.1 Safari/604.3.5";
        $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64; rv:52.0) Gecko/20100101 Firefox/52.0";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:57.0) Gecko/20100101 Firefox/57.0";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:52.0) Gecko/20100101 Firefox/52.0";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36 OPR/49.0.2725.64";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; rv:57.0) Gecko/20100101 Firefox/57.0";
        $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/604.4.7 (KHTML, like Gecko) Version/11.0.2 Safari/604.4.7";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:57.0) Gecko/20100101 Firefox/57.0";
        $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/62.0.3202.94 Chrome/62.0.3202.94 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:58.0) Gecko/20100101 Firefox/58.0";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:52.0) Gecko/20100101 Firefox/52.0";
        $userAgentArray[] = "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0;  Trident/5.0)";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; rv:52.0) Gecko/20100101 Firefox/52.0";
        $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/63.0.3239.84 Chrome/63.0.3239.84 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:57.0) Gecko/20100101 Firefox/57.0";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:56.0) Gecko/20100101 Firefox/56.0";
        $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.108 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.89 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0;  Trident/5.0)";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.1.2 Safari/603.3.8";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:57.0) Gecko/20100101 Firefox/57.0";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/604.3.5 (KHTML, like Gecko) Version/11.0.1 Safari/604.3.5";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.1.2 Safari/603.3.8";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; WOW64; rv:57.0) Gecko/20100101 Firefox/57.0";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.79 Safari/537.36 Edge/14.14393";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:56.0) Gecko/20100101 Firefox/56.0";
        $userAgentArray[] = "Mozilla/5.0 (iPad; CPU OS 11_1_2 like Mac OS X) AppleWebKit/604.3.5 (KHTML, like Gecko) Version/11.0 Mobile/15B202 Safari/604.1";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; Touch; rv:11.0) like Gecko";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.13; rv:58.0) Gecko/20100101 Firefox/58.0";
        $userAgentArray[] = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Safari/604.1.38";
        $userAgentArray[] = "Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36";
        $userAgentArray[] = "Mozilla/5.0 (X11; CrOS x86_64 9901.77.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.97 Safari/537.36";

        $index = array_rand($userAgentArray);

        return $userAgentArray[$index];
    }
}
