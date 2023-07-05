<?php

namespace App\Console\Commands;

use App\Lead;
use Illuminate\Console\Command;

class Missing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'missing:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        collect($this->rony())
            ->reject(fn ($click) => Lead::whereDate('created_at', now())
            ->where('clickid', $click)->exists())->dd();
    }

    public function rony()
    {
        return [
            '5f07575bd167e94d9e321c35',
            '5f075613d167e94d9e321c28',
            '5f075460d167e94d9e321c1f',
            '5f074a54d167e94d9e321bee',
            '5f0749b0d167e94d9e321beb',
            '5f074908d167e94d9e321be5',
            '5f0748b9d167e94d9e321bd7',
            '5f0740d7d167e94d9e321b7f',
            '5f073d7dd167e94d9e321b76',
            '5f073c49d167e94d9e321b6d',
            '5f07386be28775491c2ef829',
            '5f0733a1e28775491c2ef81c',
            '5f073362e28775491c2ef816',
            '5f072f02c7b110340a617999',
            '5f0729b9c7b110340a617983',
            '5f072976c7b110340a61797c',
            '5f072729c7b110340a617967',
            '5f0725dec7b110340a617960',
            '5f0722aac7b110340a617946',
            '5f0721a2c7b110340a61793d',
            '5f0720d4c7b110340a617934',
            '5f071f85c7b110340a617926',
            '5f071c5dc7b110340a617914',
            '5f0718e6c7b110340a617901',
            '5f07188fc7b110340a6178fe',
            '5f07176bc7b110340a6178fb',
            '5f070c4ec7b110340a6178d4',
            '5f070b85c7b110340a6178cd',
            '5f07080ec7b110340a6178b0',
            '5f0701b3c7b110340a61788f',
            '5f0701b0c7b110340a61788c',
            '5f06fff1c7b110340a617889',
            '5f06ffa4c7b110340a61787b',
            '5f06ff7fc7b110340a617874',
            '5f06ff0bc7b110340a617871',
            '5f06fc4ec7b110340a61785c',
            '5f06fc19c7b110340a617859',
            '5f06fbeec7b110340a617856',
            '5f06f9e1c7b110340a61784f',
            '5f06f8f8c7b110340a617849',
            '5f06f8f5c7b110340a617846',
            '5f06f733c7b110340a61783e',
            '5f06f592c7b110340a61782b',
            '5f06f4c8c7b110340a617819',
            '5f06f0fbce749d5e253cc898',
            '5f06ece8ce749d5e253cc86c',
            '5f06ebb6ce749d5e253cc860',
            '5f06ea47ce749d5e253cc854',
            '5f06ea38ce749d5e253cc851',
            '5f06e9c7ce749d5e253cc84e',
            '5f06e992ce749d5e253cc84b',
            '5f06e7c7ce749d5e253cc83c',
            '5f06e77ece749d5e253cc839',
            '5f06e734ce749d5e253cc833',
            '5f06e636ce749d5e253cc825',
            '5f06e506ce749d5e253cc820',
            '5f06e432ce749d5e253cc81c',
            '5f06e3adce749d5e253cc80d',
            '5f06e0bace749d5e253cc805',
            '5f06de6fce749d5e253cc7f1',
            '5f06de44ce749d5e253cc7e6',
            '5f06dda4ce749d5e253cc7e0',
            '5f06dcffce749d5e253cc7dd',
            '5f06dc74ce749d5e253cc7d2',
            '5f06dc74ce749d5e253cc7d0',
            '5f06dab0ce749d5e253cc7ca',
            '5f06d910ce749d5e253cc7ba',
            '5f06d8b7ce749d5e253cc7b7',
            '5f06d811ce749d5e253cc7aa',
            '5f06d780ce749d5e253cc79e',
            '5f06ccd5ce749d5e253cc71a',
            '5f06cb6dce749d5e253cc6fc',
            '5f06c4b2ce749d5e253cc6c1',
            '5f06c43ece749d5e253cc6be',
            '5f06c23cce749d5e253cc6b7',
            '5f06c23ace749d5e253cc6b4',
            '5f06c211ce749d5e253cc6ae',
            '5f06c1d6ce749d5e253cc6a8',
            '5f06c190ce749d5e253cc6a5',
            '5f06c14cce749d5e253cc69f',
            '5f06c135ce749d5e253cc69c',
            '5f06c0d6ce749d5e253cc699',
            '5f06c06ece749d5e253cc695',
            '5f06bfc4ce749d5e253cc68f',
            '5f06bf28ce749d5e253cc68c',
            '5f06bdc2ce749d5e253cc67d',
            '5f06bc8bce749d5e253cc677',
            '5f06bc38ce749d5e253cc674',
            '5f06bbd9ce749d5e253cc671',
            '5f06b9e7ce749d5e253cc66d',
            '5f06b9a1ce749d5e253cc66a',
            '5f06b972ce749d5e253cc667',
            '5f06b8c7ce749d5e253cc663',
            '5f06b8bbce749d5e253cc660',
            '5f06a68ace749d5e253cc600',
            '5f06a308ce749d5e253cc5f6',
            '5f069a5ace749d5e253cc5e0',
            '5f069792ce749d5e253cc5d3',
            '5f0694c5ce749d5e253cc5c6',
            '5f069176ce749d5e253cc5b5',
            '5f068fa3ce749d5e253cc5af',
            '5f068e84ce749d5e253cc5a8',
            '5f068b37ce749d5e253cc59b',
            '5f068b2ace749d5e253cc598',
            '5f068a01ce749d5e253cc591',
            '5f068974ce749d5e253cc58b',
            '5f068816ce749d5e253cc584',
            '5f068499ce749d5e253cc57d',
            '5f0683bbce749d5e253cc577',
            '5f067c47ce749d5e253cc567',
            '5f067a09ce749d5e253cc560',
            '5f066f7ece749d5e253cc547',
            '5f066d09ce749d5e253cc540',
            '5f066c65ce749d5e253cc53a',
            '5f066a64ce749d5e253cc533',
            '5f0664b2ce749d5e253cc521',
            '5f065f4fce749d5e253cc513',
            '5f065df1ce749d5e253cc50f',
            '5f065db9ce749d5e253cc50c',
            '5f065b30ce749d5e253cc508',
            '5f0657f1ce749d5e253cc504',
            '5f0657adce749d5e253cc501',
            '5f06529ece749d5e253cc4fc',
            '5f065261ce749d5e253cc4f3',
            '5f0651b8ce749d5e253cc4ef',
            '5f065168ce749d5e253cc4ec',
            '5f064ff4ce749d5e253cc4e9',
            '5f064cecce749d5e253cc4d8',
            '5f0649e6ce749d5e253cc4d4',
            '5f06487ace749d5e253cc4d0',
            '5f0647edce749d5e253cc4cd',
            '5f06468ece749d5e253cc4c6',
            '5f064487ce749d5e253cc4c0',
            '5f063fd8ce749d5e253cc4b5',
            '5f0637c0ce749d5e253cc49c',
            '5f06094ece749d5e253cc47a',
            '5f0608b6ce749d5e253cc477',
            '5f06068fce749d5e253cc473',
            '5f06032cce749d5e253cc46f',
            '5f0601c2ce749d5e253cc46b',
            '5f060124ce749d5e253cc468',
            '5f0600abce749d5e253cc465',
            '5f06000ece749d5e253cc461',
            '5f05fed8ce749d5e253cc45e',
            '5f05fe79ce749d5e253cc45b',
            '5f05fc46ce749d5e253cc457',
            '5f05fbc2ce749d5e253cc454',
            '5f05faf6ce749d5e253cc450',
            '5f05fa2bce749d5e253cc44d',
            '5f05f940ce749d5e253cc44a',
        ];
    }
}
