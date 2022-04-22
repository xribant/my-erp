<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Invoice;
use App\Entity\Customer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $user = new User();

        $user->setFirstName('Xavier')
            ->setLastName('Ribant')
            ->setEmail('xribant@gmail.com')
            ->setPassword($this->passwordHasher->hashPassword($user, 'Helix2014'))
        ;

        $manager->persist($user);

        // Customers et Invoices pour l'utilisateur Xavier
        $count = 1;

        for ($c = 0; $c < 25; $c++) {
            $customer = new Customer();
            $customer->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setCompany($faker->company())
                ->setEmail($faker->email())
                ->setUser($user)
            ;

            $manager->persist($customer);

            //Création d'une série de factures
            for($i = 0; $i < mt_rand(3, 10); $i++) {
                $invoice = new Invoice();
                $date = new DateTime();
                $invoice->setAmount($faker->randomFloat(2, 250, 5000))
                    ->setNumber($date->format('Y').'-'.$count)
                    ->setSentAt($faker->dateTimeBetween('-6 months'))
                    ->setStatus($faker->randomElement(['SENT', 'PAID', 'CANCELLED']))
                    ->setCustomer($customer);
                $count++;
                $manager->persist($invoice);

            }
        };

        // Création d'une série d'autres utilisateurs
        for($u=0;$u<20;$u++){

            $count = 1;
            $user = new User();

            $hash = $this->passwordHasher->hashPassword($user, 'Helix2014');

            $user->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setEmail($faker->email())
                ->setPassword($hash)
            ;

            $manager->persist($user);

            // Création d'une série de clients
            for ($c = 0; $c<mt_rand(2, 20); $c++) {
                $customer = new Customer();
                $customer->setFirstName($faker->firstName())
                    ->setLastName($faker->lastName())
                    ->setCompany($faker->company())
                    ->setEmail($faker->email())
                    ->setUser($user)
                ;

                $manager->persist($customer);

                //Création d'une série de factures
                for($i = 0; $i < mt_rand(3, 10); $i++) {
                    $invoice = new Invoice();
                    $date = new DateTime();
                    $invoice->setAmount($faker->randomFloat(2, 250, 5000))
                        ->setNumber($date->format('Y').'-'.$count)
                        ->setSentAt($faker->dateTimeBetween('-6 months'))
                        ->setStatus($faker->randomElement(['SENT', 'PAID', 'CANCELLED']))
                        ->setCustomer($customer);
                    $count++;
                    $manager->persist($invoice);

                }
            };
        }
        
        $manager->flush();
    }
}
