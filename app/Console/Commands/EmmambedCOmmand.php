<?php

namespace App\Console\Commands;

use App\Models\Complaint;
use App\Services\SimilarityService;
use Illuminate\Console\Command;

class EmbedComplaints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:embed-complaints';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
   public function handle(SimilarityService $sim)
{
    $batch = Complaint::whereNull('embedding')->limit(500)->get();
    foreach ($batch as $c) {
        $emb = $sim->getEmbedding($c->description);
        if ($emb) {
            $c->embedding = json_encode($emb);
            $c->save();
            $this->info("Embedded complaint {$c->id}");
        } else {
            $this->error("Failed embed {$c->id}");
        }
        sleep(1); // be gentle
    }
}

}
