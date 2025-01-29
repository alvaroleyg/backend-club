<?php

namespace App\Tests\Entity;

use App\Entity\Club;
use App\Entity\Player;
use App\Entity\Coach;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClubTest extends KernelTestCase
{
    private $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::$container->get(ValidatorInterface::class);
    }

    /**
     * @covers \App\Entity\Club
     */
    public function emptyName(): void {
        $club = new Club();
        $club->setName('');
        $club->setBudget(1000);
        
        $errors = $this->validator->validate($club);
        $this->assertCount(1, $errors);
    }

    /**
     * @covers \App\Entity\Club
     */
    public function testCalculateTotalSalaries(): void {
        $club = new Club();
        $player = (new Player())->setSalary(1000);
        $coach = (new Coach())->setSalary(2000);
        
        $club->addPlayer($player);
        $club->addCoach($coach);
        
        $this->assertEquals(3000, $club->calculateTotalSalaries());
    }
}