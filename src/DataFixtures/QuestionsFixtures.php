<?php

namespace App\DataFixtures;

use App\Entity\Questions;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class QuestionsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        /*for($i = 1; $i <= 10; $i++){
            $question = new Questions();
            $question->setQuestion("Question N°$i");
            $manager->persist($question);
        }*/
        $manager->flush();
    }
}
