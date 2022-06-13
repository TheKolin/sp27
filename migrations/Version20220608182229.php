<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220608182229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `group` (id INT AUTO_INCREMENT NOT NULL, number INT NOT NULL, sub_group VARCHAR(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson (id INT AUTO_INCREMENT NOT NULL, subject INT NOT NULL, teacher INT NOT NULL, lesson_type VARCHAR(2) NOT NULL, day_of_week INT NOT NULL, time INT NOT NULL, students_count INT NOT NULL, INDEX IDX_F87474F3FBCE3E7A (subject), INDEX IDX_F87474F3B0F6A6D5 (teacher), INDEX IDX_F87474F3B5AF914D (lesson_type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_type (code VARCHAR(2) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, student INT NOT NULL, lesson INT NOT NULL, reservation_status VARCHAR(2) NOT NULL, INDEX IDX_42C84955B723AF33 (student), INDEX IDX_42C84955F87474F3 (lesson), INDEX IDX_42C8495533F12C87 (reservation_status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation_status (code VARCHAR(2) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subject (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, user_type VARCHAR(2) NOT NULL, user_status VARCHAR(2) NOT NULL, class INT DEFAULT NULL, uuid VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649D17F50A6 (uuid), INDEX IDX_8D93D649F65F1BE0 (user_type), INDEX IDX_8D93D6491E527E21 (user_status), INDEX IDX_8D93D649ED4B199F (class), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_status (code VARCHAR(2) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_type (code VARCHAR(2) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3FBCE3E7A FOREIGN KEY (subject) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3B0F6A6D5 FOREIGN KEY (teacher) REFERENCES user (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3B5AF914D FOREIGN KEY (lesson_type) REFERENCES lesson_type (code)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955B723AF33 FOREIGN KEY (student) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955F87474F3 FOREIGN KEY (lesson) REFERENCES lesson (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495533F12C87 FOREIGN KEY (reservation_status) REFERENCES reservation_status (code)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F65F1BE0 FOREIGN KEY (user_type) REFERENCES user_type (code)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6491E527E21 FOREIGN KEY (user_status) REFERENCES user_status (code)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649ED4B199F FOREIGN KEY (class) REFERENCES `group` (id)');

        // add consts
        $this->addSql("INSERT INTO user_type VALUES ('ST', 'Uczeń')");
        $this->addSql("INSERT INTO user_type VALUES ('TE', 'Nauczyciel')");
        $this->addSql("INSERT INTO user_status VALUES ('CR', 'Utworzony')");
        $this->addSql("INSERT INTO user_status VALUES ('AC', 'Aktywny')");
        $this->addSql("INSERT INTO user_status VALUES ('AR', 'Zarchiwizowany')");
        $this->addSql("INSERT INTO reservation_status VALUES ('CR', 'Utworzony')");
        $this->addSql("INSERT INTO reservation_status VALUES ('DN', 'Ukończony')");
        $this->addSql("INSERT INTO reservation_status VALUES ('UN', 'Nieukończony')");
        $this->addSql("INSERT INTO lesson_type VALUES ('FX', 'Stały')");
        $this->addSql("INSERT INTO lesson_type VALUES ('CH', 'Wybieralny')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649ED4B199F');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955F87474F3');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3B5AF914D');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495533F12C87');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3FBCE3E7A');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3B0F6A6D5');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955B723AF33');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6491E527E21');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F65F1BE0');
        $this->addSql('DROP TABLE `group`');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE lesson_type');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE reservation_status');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_status');
        $this->addSql('DROP TABLE user_type');
    }
}
