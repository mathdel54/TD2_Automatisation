<?php

namespace App\Console;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Office;
use Illuminate\Support\Facades\Schema;
use Slim\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Faker\Factory;

class PopulateDatabaseCommand extends Command
{
    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('db:populate');
        $this->setDescription('Populate database');
    }

    protected function execute(InputInterface $input, OutputInterface $output ): int
    {
        $output->writeln('Populate database...');

        /** @var \Illuminate\Database\Capsule\Manager $db */
        $db = $this->app->getContainer()->get('db');

        $db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=0");
        $db->getConnection()->statement("TRUNCATE `employees`");
        $db->getConnection()->statement("TRUNCATE `offices`");
        $db->getConnection()->statement("TRUNCATE `companies`");
        $db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=1");

        $faker = Factory::create('fr_FR');
        
        for ($i = 1; $i <= 4; $i++) {
            $this->createCompany($i, $faker, $db);
        }
        $count = 1;
        for ($i = 1; $i <= 4; $i++) {
            for ($j = 1; $j <= 2; $j++) {
                $this->createOffices($count++, $i, $faker, $db);
            }
        }
        $count = 1;
        for ($i = 1; $i <= 8; $i++) {
            $this->createEmployees($count++, $i, $faker, $db);
        }
        

        $db->getConnection()->statement("update companies set head_office_id = 1 where id = 1;");
        $db->getConnection()->statement("update companies set head_office_id = 3 where id = 2;");

        $output->writeln('Database created successfully!');
        return 0;
    }

    private function createCompany($id, $faker, $db): void
    {
        $insert = "INSERT INTO `companies` VALUES ";
        $name = $faker->company;
        $phone = $faker->phoneNumber;
        $email = $faker->companyEmail;
        $website = $faker->url;
        $image = $faker->imageUrl(800, 600, 'business');
        $createdAt = $faker->dateTimeThisDecade->format('Y-m-d H:i:s');
        $updatedAt = $faker->dateTimeThisDecade->format('Y-m-d H:i:s');
        $insert .= "($id, '$name', '$phone', '$email', '$website', '$image', '$createdAt', '$updatedAt', null)";

        $db->getConnection()->statement($insert);
    }

    private function createOffices($id, $companyId, $faker, $db): void
    {
        $insert = "INSERT INTO `offices` VALUES ";
        $name = $faker->company . ' Office';
        $address = $faker->streetAddress;
        $city = $faker->city;
        $zipCode = $faker->postcode;
        $country = $faker->country;
        $email = $faker->optional()->companyEmail;
        $phone = $faker->optional()->phoneNumber;
        $createdAt = $faker->dateTimeThisDecade->format('Y-m-d H:i:s');
        $updatedAt = $faker->dateTimeThisDecade->format('Y-m-d H:i:s');
        $insert .= "($id, '$name', '$address', '$city', '$zipCode', '$country', '$email', '$phone', $companyId, '$createdAt', '$updatedAt')";

        $db->getConnection()->statement($insert);
    }

    private function createEmployees($id, $officeId, $faker, $db): void
    {
        $insert = "INSERT INTO `employees` VALUES ";
        $firstName = $faker->firstName;
        $lastName = $faker->lastName;
        $email = $faker->optional()->companyEmail;
        $phone = $faker->optional()->phoneNumber;
        $jobTitle = $faker->jobTitle;
        $createdAt = $faker->dateTimeThisDecade->format('Y-m-d H:i:s');
        $updatedAt = $faker->dateTimeThisDecade->format('Y-m-d H:i:s');
        $insert .= "($id, '$firstName', '$lastName', $officeId, '$email', '$phone', \"$jobTitle\", '$createdAt', '$updatedAt')";

        $db->getConnection()->statement($insert);
    }
}
