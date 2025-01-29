<?php

namespace App\Tests\Entity;

use App\Entity\Coach;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CoachTest extends KernelTestCase
{
    private $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::$container->get(ValidatorInterface::class);
    }

    /**
     * @covers \App\Entity\Coach
     */
    public function testValidcoach()
    {
        $coach = new Coach();
        $coach->setName('Alex Ferguson');
        $coach->setAge(72);
        $coach->setSalary(800000);

        $errors = $this->validator->validate($coach);
        $this->assertCount(0, $errors);
    }

    /**
     * @covers \App\Entity\Coach
     */
    public function testInvalidcoach()
    {
        $coach = new Coach();
        $coach->setName('');
        $coach->setAge(15);
        $coach->setSalary(-1000);

        $errors = $this->validator->validate($coach);
        $this->assertGreaterThan(0, count($errors));
    }

    /**
     * @covers \App\Entity\Coach
     */
    public function negativeSalary(): void {
        $coach = new coach();
        $coach->setSalary(-500);
        
        $errors = $this->validator->validateProperty($coach, 'salary');
        $this->assertNotEmpty($errors);
    }
}
