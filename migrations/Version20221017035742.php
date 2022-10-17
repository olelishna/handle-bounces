<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221017035742 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE bounced_item (feedback_id VARCHAR(255) NOT NULL, mail_id VARCHAR(255) NOT NULL, recipient_id VARCHAR(255) NOT NULL, bounce_type VARCHAR(255) NOT NULL, bounce_sub_type VARCHAR(255) NOT NULL, action VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, diagnostic_code LONGTEXT DEFAULT NULL, timestamp DATETIME NOT NULL, reporting_mta VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_68C269A2C8776F01 (mail_id), INDEX IDX_68C269A2E92F8F78 (recipient_id), INDEX bounce_type_idx (bounce_type), INDEX bounce_sub_type_idx (bounce_sub_type), PRIMARY KEY(feedback_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE complaint_item (feedback_id VARCHAR(255) NOT NULL, mail_id VARCHAR(255) NOT NULL, recipient_id VARCHAR(255) NOT NULL, complaint_sub_type VARCHAR(255) DEFAULT NULL, timestamp DATETIME NOT NULL, arrival_date DATETIME NOT NULL, complaint_feedback_type VARCHAR(255) DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_C01AF3B5C8776F01 (mail_id), INDEX IDX_C01AF3B5E92F8F78 (recipient_id), INDEX complaint_sub_type_idx (complaint_sub_type), PRIMARY KEY(feedback_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE suppressed_client (email VARCHAR(255) NOT NULL, score DOUBLE PRECISION DEFAULT NULL, created DATETIME DEFAULT NULL, updated DATETIME DEFAULT NULL, INDEX score_idx (score), PRIMARY KEY(email)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE suppressed_mail (message_id VARCHAR(255) NOT NULL, destination VARCHAR(255) NOT NULL, source VARCHAR(255) NOT NULL, subject VARCHAR(1024) NOT NULL, timestamp DATETIME NOT NULL, PRIMARY KEY(message_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE bounced_item ADD CONSTRAINT FK_68C269A2C8776F01 FOREIGN KEY (mail_id) REFERENCES suppressed_mail (message_id)'
        );
        $this->addSql(
            'ALTER TABLE bounced_item ADD CONSTRAINT FK_68C269A2E92F8F78 FOREIGN KEY (recipient_id) REFERENCES suppressed_client (email)'
        );
        $this->addSql(
            'ALTER TABLE complaint_item ADD CONSTRAINT FK_C01AF3B5C8776F01 FOREIGN KEY (mail_id) REFERENCES suppressed_mail (message_id)'
        );
        $this->addSql(
            'ALTER TABLE complaint_item ADD CONSTRAINT FK_C01AF3B5E92F8F78 FOREIGN KEY (recipient_id) REFERENCES suppressed_client (email)'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bounced_item DROP FOREIGN KEY FK_68C269A2C8776F01');
        $this->addSql('ALTER TABLE bounced_item DROP FOREIGN KEY FK_68C269A2E92F8F78');
        $this->addSql('ALTER TABLE complaint_item DROP FOREIGN KEY FK_C01AF3B5C8776F01');
        $this->addSql('ALTER TABLE complaint_item DROP FOREIGN KEY FK_C01AF3B5E92F8F78');
        $this->addSql('DROP TABLE bounced_item');
        $this->addSql('DROP TABLE complaint_item');
        $this->addSql('DROP TABLE suppressed_client');
        $this->addSql('DROP TABLE suppressed_mail');
    }
}
