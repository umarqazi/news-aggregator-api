<?php

namespace App\Console\Commands;

use App\Services\NewsAggregatorService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store articles from multiple news sources';

    protected NewsAggregatorService $newsAggregatorService;

    public function __construct(NewsAggregatorService $newsAggregatorService)
    {
        parent::__construct();
        $this->newsAggregatorService = $newsAggregatorService;
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws GuzzleException
     */
    public function handle(): void
    {
        $this->info('Fetching Articles...');
        Log::info('Fetching Articles...');

        // Fetch and Store Articles in Database.
        $this->newsAggregatorService->fetchAndStoreArticles();

        $this->info('Articles fetched and stored successfully.');
        Log::info('Articles fetched.');
    }
}
