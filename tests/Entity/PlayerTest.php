<?php

namespace App\Tests\Entity;

use App\Entity\Player;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PlayerTest extends KernelTestCase
{
    private $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::$container->get(ValidatorInterface::class);
    }

    /**
     * @covers \App\Entity\Player
     */
    public function testValidPlayer()
    {
        $player = new Player();
        $player->setName('Lionel Messi');
        $player->setAge(30);
        $player->setSalary(1000000);

        $errors = $this->validator->validate($player);
        $this->assertCount(0, $errors);
    }

    /**
     * @covers \App\Entity\Player
     */
    public function testInvalidPlayer()
    {
        $player = new Player();
        $player->setName('');
        $player->setAge(15);
        $player->setSalary(-1000);

        $errors = $this->validator->validate($player);
        $this->assertGreaterThan(0, count($errors));
    }

    /**
     * @covers \App\Entity\Player
     */
    public function negativeSalary(): void {
        $player = new Player();
        $player->setSalary(-500);
        
        $errors = $this->validator->validateProperty($player, 'salary');
        $this->assertNotEmpty($errors);
    }
}
