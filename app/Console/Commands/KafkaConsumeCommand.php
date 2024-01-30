<?php

namespace App\Console\Commands;

use App\Jobs\DepartmentJob;
use App\Jobs\EmployeeJob;
use App\Jobs\OrganizationJob;
use App\Jobs\SubOrganizationJob;
use App\Jobs\UserJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Junges\Kafka\Contracts\KafkaConsumerMessage;
use Junges\Kafka\Facades\Kafka;

class KafkaConsumeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consume';

    private $payload = [];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $consumer = Kafka::createConsumer(
            [
                'um.public.users',
                'um.public.departments',
                'um.public.employees',
                'um.public.designations',
                'um.public.positions'
            ]
        )
            ->withHandler(function (KafkaConsumerMessage $message) {


                $messageBody = $message->getBody(); // Convert JSON to associative array
                $this->info('Received payload: .' . json_encode($messageBody));

                if (isset($messageBody['payload'])) {
                    $payload = $messageBody['payload'];

                    // Handle the payload data as needed
                    $before = $payload['before'];
                    $after = $payload['after'];
                    $source = $payload['source'];
                    $operation = $payload['op'];

                    // Your further processing logic goes here
                    $this->info('Received payload: .' . json_encode($payload));
                    $this->info('Received before: .' . json_encode($before));
                    $this->info('Received after: .' . json_encode($after));
                    $this->info('Received source: .' . json_encode($source));
                    $this->info('Received operation: .' . json_encode($operation));
                    // info($payload);
                    $this->payload = $payload;
                    info($this->payload);
                    $this->dispatchJob($payload['source']);
                } else {
                    $this->error('Invalid message format: Payload missing');
                }
            })->build();

        $consumer->consume();
    }
    private function dispatchJob(array $source)
    {
        if ($source['table'] == 'users') {
            return Bus::dispatch(new UserJob($this->payload));
        }

        if ($source['table'] == 'departments') {
            return Bus::dispatch(new DepartmentJob($this->payload));
        }

        if ($source['table'] == 'employees') {
            return Bus::dispatch(new EmployeeJob($this->payload));
        }

        if ($source['table'] == 'organizations') {
            return Bus::dispatch(new OrganizationJob($this->payload));
        }

        if ($source['table'] == 'suborganizations') {
            return Bus::dispatch(new SubOrganizationJob($this->payload));
        }
    }
}
