<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250127113755 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coach CHANGE age age INT DEFAULT NULL, CHANGE salary salary DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE player CHANGE age age INT DEFAULT NULL, CHANGE salary salary DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coach CHANGE age age INT NOT NULL, CHANGE salary salary DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE player CHANGE age age INT NOT NULL, CHANGE salary salary DOUBLE PRECISION NOT NULL');
    }
}
