<?php

namespace App\DataFixtures;

use App\Entity\Choices;
use App\Entity\Questions;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ChoicesFixtures extends Fixture

{
    public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 3; $i++){

            $question = new Questions();
            $question->setQuestion("Question");
            $manager->persist($question);
            for($j = 1; $j <= 3; $j++) {
                $choice = new Choices();
                $choice->setQuestion($question);
                $choice->setChoice("Question , Choice N°$j");
                if($j==1){
                    $choice->setValidity(true);
                }
                else{
                    $choice->setValidity(false);
                }
                $manager->persist($choice);
            }
        }
        $manager->flush();
    }
}
