<?php
declare(strict_types=1);


namespace App\DataFixtures;


use App\Entity\CorrectDecision;
use App\Entity\Decision;
use App\Entity\Questionnaire;
use App\Entity\Task;
use App\Enum\TaskDecisionType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private ObjectManager $manager;

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->makeMathQuestionnaire();
        $this->makeMixedQuestionnaire();

        $manager->flush();
    }

    private function makeMathQuestionnaire(): void
    {
        $questionnaire = new Questionnaire();
        $questionnaire->setName('Тест математика');
        $this->manager->persist($questionnaire);
        $task = $this->makeTask('1 + 1', $questionnaire);
        $this->makeDecision($task, '3');
        $this->makeDecision($task, '2', true);
        $this->makeDecision($task, '0');

        $task = $this->makeTask('2 + 2', $questionnaire);
        $this->makeDecision($task, '4', true);
        $this->makeDecision($task, '3 + 1', true);
        $this->makeDecision($task, '10');

        $task = $this->makeTask('3 + 3', $questionnaire);
        $this->makeDecision($task, '1 + 5', true);
        $this->makeDecision($task, '1');
        $this->makeDecision($task, '6', true);
        $this->makeDecision($task, '2 + 4', true);

        $task = $this->makeTask('4 + 4', $questionnaire);
        $this->makeDecision($task, '8', true);
        $this->makeDecision($task, '4');
        $this->makeDecision($task, '0');
        $this->makeDecision($task, '0 + 8', true);

        $task = $this->makeTask('5 + 5', $questionnaire);
        $this->makeDecision($task, '6');
        $this->makeDecision($task, '18');
        $this->makeDecision($task, '10', true);
        $this->makeDecision($task, '9');
        $this->makeDecision($task, '0');

        $task = $this->makeTask('6 + 6', $questionnaire);
        $this->makeDecision($task, '3');
        $this->makeDecision($task, '9');
        $this->makeDecision($task, '0');
        $this->makeDecision($task, '12', true);
        $this->makeDecision($task, '5 + 7', true);

        $task = $this->makeTask('7 + 7', $questionnaire);
        $this->makeDecision($task, '5');
        $this->makeDecision($task, '14', true);

        $task = $this->makeTask('8 + 8', $questionnaire);
        $this->makeDecision($task, '16', true);
        $this->makeDecision($task, '12');
        $this->makeDecision($task, '9');
        $this->makeDecision($task, '5');

        $task = $this->makeTask('9 + 9', $questionnaire);
        $this->makeDecision($task, '18', true);
        $this->makeDecision($task, '9');
        $this->makeDecision($task, '17 + 1', true);
        $this->makeDecision($task, '2 + 16', true);

        $task = $this->makeTask('10 + 10', $questionnaire);
        $this->makeDecision($task, '0');
        $this->makeDecision($task, '2');
        $this->makeDecision($task, '8');
        $this->makeDecision($task, '20', true);
    }

    private function makeMixedQuestionnaire(): void
    {
        $questionnaire = new Questionnaire();
        $questionnaire->setName('Тест с разными типами решений и количеством очков за ответ');
        $this->manager->persist($questionnaire);

        $task = $this->makeTask(
            'Какой лучший файлобменник?',
            $questionnaire,
            TaskDecisionType::SINGLE_SELECT
        );

        $this->makeDecision($task, 'VK');
        $this->makeDecision($task, 'Skype', true);
        $this->makeDecision($task, 'Doka 2');

        $task = $this->makeTask(
            'Каким выражением можно заменить оператор case в PHP начиная с 8 версии?',
            $questionnaire,
            TaskDecisionType::INPUT_TEXT
        );

        $this->makeInputDecision($task, 'match', 1);
        $this->makeInputDecision($task, 'match(){}', 3);
        $this->makeInputDecision($task, 'match()', 2);

        $task = $this->makeTask(
            'Какой из перечисленных фреймворков основан на Symfony или его компонентах',
            $questionnaire
        );

        $this->makeDecision($task, 'Laravel', true);
        $this->makeDecision($task, 'Api-platform', true);
        $this->makeDecision($task, 'Silex', true);
    }

    private function makeDecision(Task $task, string $title, bool $correct = false): void
    {
        $decision = new Decision();
        $decision->setTitle($title);
        $this->manager->persist($decision);
        $decision->setTask($task);


        if ($correct) {
            $correctDecision = new CorrectDecision();
            $correctDecision->setDecision($decision);
            $this->manager->persist($correctDecision);
            $correctDecision->setTask($task);
        }
    }

    private function makeInputDecision(Task $task, string $answer, int $cost = 1)
    {
        $correctDecision = new CorrectDecision();
        $correctDecision->setInputValue($answer);
        $correctDecision->setCost($cost);
        $this->manager->persist($correctDecision);
        $correctDecision->setTask($task);
    }

    private function makeTask(
        string $description,
        Questionnaire $questionnaire,
        TaskDecisionType $decisionType = TaskDecisionType::MULTI_SELECT
    ): Task {
        $task = new Task();
        $task->setDescription($description);
        $task->setQuestionnaire($questionnaire);
        $task->setDecisionType($decisionType);
        $task->setPriority(rand(0, 1000));
        $this->manager->persist($task);

        return $task;
    }
}