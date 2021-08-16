<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface */
    protected UserPasswordEncoderInterface $encoder;

    /**
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');

        $adminUser = new User();

        $adminUser->setFirstName("Mickael")
            ->setLastName("RANDRIANANTENAINA")
            ->setEmail("admin@gmail.com")
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($this->encoder->encodePassword($adminUser, "password"));

        $manager->persist($adminUser);

        for ($u=0; $u < 10; $u++) { 
            $user = new User();
            $chrono = 1;
            $user->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setEmail($faker->email())
                ->setPassword($this->encoder->encodePassword($user, "password"));

                $manager->persist($user);

                for ($c=0; $c < mt_rand(5, 80); $c++) { 
                    $customer = new Customer();
                    $customer->setFirstName($faker->firstName())
                        ->setLastName($faker->lastName())
                        ->setEmail($faker->email())
                        ->setCompany($faker->company())
                        ->setUser($user);
        
                    $manager->persist($customer);
        
                    for ($i=0; $i < mt_rand(3, 30); $i++) { 
                        $invoice = new Invoice();
                        $invoice->setAmount($faker->randomFloat(2, 250, 5000))
                            ->setSentAt($faker->dateTimeBetween('- 6 months'))
                            ->setStatus($faker->randomElement(['SENT', 'PAID', 'CANCELED']))
                            ->setCustomer($customer)
                            ->setChrono($chrono);
        
                        $chrono++;
        
                        $manager->persist($invoice);
                    }
                }
        }
        $manager->flush();
    }
}