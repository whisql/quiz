<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231101085942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TYPE project_condition AS ENUM ('ACTIVE', 'COMPLETED');");
        $this->addSql("CREATE TYPE project_task_condition AS ENUM ('PENDING', 'ACTIVE', 'COMPLETED');");
        $this->addSql("CREATE TYPE project_task_order_strategy AS ENUM ('DIRECT', 'SHUFFLE', 'PRIORITY');");
        $this->addSql('CREATE TABLE correct_decision (id SERIAL NOT NULL, task_id INT NOT NULL, decision_id INT DEFAULT NULL, input_value VARCHAR(255) DEFAULT NULL, cost INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F12D6B3C8DB60186 ON correct_decision (task_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F12D6B3CBDEE7539 ON correct_decision (decision_id)');
        $this->addSql('CREATE TABLE decision (id SERIAL NOT NULL, task_id INT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_84ACBE488DB60186 ON decision (task_id)');
        $this->addSql('CREATE TABLE project (id SERIAL NOT NULL, questionnaire_id INT NOT NULL, condition project_condition, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, completed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, task_order_strategy project_task_order_strategy, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2FB3D0EECE07E8FF ON project (questionnaire_id)');
        $this->addSql('CREATE TABLE project_decision (id SERIAL NOT NULL, decision_id INT DEFAULT NULL, project_task_id INT NOT NULL, input_value VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B9E23593BDEE7539 ON project_decision (decision_id)');
        $this->addSql('CREATE INDEX IDX_B9E235931BA80DE3 ON project_decision (project_task_id)');
        $this->addSql('CREATE TABLE project_task (id SERIAL NOT NULL, task_id INT NOT NULL, project_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, completed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, activated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, condition project_task_condition, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6BEF133D8DB60186 ON project_task (task_id)');
        $this->addSql('CREATE INDEX IDX_6BEF133D166D1F9C ON project_task (project_id)');
        $this->addSql('CREATE TABLE questionnaire (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE task (id SERIAL NOT NULL, questionnaire_id INT NOT NULL, description TEXT NOT NULL, decision_type VARCHAR(255) NOT NULL, priority INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_527EDB25CE07E8FF ON task (questionnaire_id)');
        $this->addSql('ALTER TABLE correct_decision ADD CONSTRAINT FK_F12D6B3C8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE correct_decision ADD CONSTRAINT FK_F12D6B3CBDEE7539 FOREIGN KEY (decision_id) REFERENCES decision (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE decision ADD CONSTRAINT FK_84ACBE488DB60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EECE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES questionnaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_decision ADD CONSTRAINT FK_B9E23593BDEE7539 FOREIGN KEY (decision_id) REFERENCES decision (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_decision ADD CONSTRAINT FK_B9E235931BA80DE3 FOREIGN KEY (project_task_id) REFERENCES project_task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_task ADD CONSTRAINT FK_6BEF133D8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project_task ADD CONSTRAINT FK_6BEF133D166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25CE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES questionnaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TYPE project_condition");
        $this->addSql("DROP TYPE project_task_condition");
        $this->addSql("DROP TYPE project_task_order_strategy");
        $this->addSql('ALTER TABLE correct_decision DROP CONSTRAINT FK_F12D6B3C8DB60186');
        $this->addSql('ALTER TABLE correct_decision DROP CONSTRAINT FK_F12D6B3CBDEE7539');
        $this->addSql('ALTER TABLE decision DROP CONSTRAINT FK_84ACBE488DB60186');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EECE07E8FF');
        $this->addSql('ALTER TABLE project_decision DROP CONSTRAINT FK_B9E23593BDEE7539');
        $this->addSql('ALTER TABLE project_decision DROP CONSTRAINT FK_B9E235931BA80DE3');
        $this->addSql('ALTER TABLE project_task DROP CONSTRAINT FK_6BEF133D8DB60186');
        $this->addSql('ALTER TABLE project_task DROP CONSTRAINT FK_6BEF133D166D1F9C');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25CE07E8FF');
        $this->addSql('DROP TABLE correct_decision');
        $this->addSql('DROP TABLE decision');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_decision');
        $this->addSql('DROP TABLE project_task');
        $this->addSql('DROP TABLE questionnaire');
        $this->addSql('DROP TABLE task');
    }
}
