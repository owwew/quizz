<?php

namespace App\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use http\Env\Response;
use phpDocumentor\Reflection\Types\Resource_;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UsersRepository;
use App\Repository\QuestionsRepository;
use App\Repository\ChoicesRepository;
use App\Repository\AnswersRepository;
use App\Entity\Users;
use App\Entity\Questions;
use App\Entity\Choices;
use App\Entity\Answers;


class QuizzController extends Controller
{



    /**
     * @Route("/", name="home")
     */
    public function home(Request $request, ObjectManager $manager)
    {
        $user = new Users();

        $form = $this->createFormBuilder($user)
            ->add('lastName')
            ->add('firstName')
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('quizz', ['id'=> $user->getId()]);
        }
        return $this->render('quizz/home.html.twig',
            ['formUser' => $form->createView()
            ]);
    }


    /**
     * @Route("/quizz/{id}", name="quizz")
     */
    public function show($id,QuestionsRepository $questionsRepository)
    {
        $usersRepository = $this->getDoctrine()->getRepository(Users::class);
        $user = $usersRepository->find($id);
        $questions = $questionsRepository->findAll();

        return $this->render('quizz/index.html.twig', [
            'questions'=>$questions,
            'user'=>$user,
        ]);
    }


    /**
     * @Route("/add_new_uestion", name="add_new_question")
     */
    public function newQuestion(Request $request, ObjectManager $manager, ChoicesRepository $choicesRepository)
    {
        $request = $this->get('request_stack')->getCurrentRequest();

        $question = new Questions();
        $question -> setQuestion($request->get('question'));
        $manager->persist($question);

        $choice = new Choices();
        $choice1 =  $request->get('choice1');
        $choice -> setQuestion($question);
        $choice -> setChoice($choice1);
        $choice -> setValidity(true);
        $manager->persist($choice);

        $choice = new Choices();
        $choice2 =  $request->get('choice2');
        $choice -> setQuestion($question);
        $choice -> setChoice($choice2);
        $choice -> setValidity(false);
        $manager->persist($choice);

        $choice = new Choices();
        $choice3 =  $request->get('choice3');
        $choice -> setQuestion($question);
        $choice -> setChoice($choice3);
        $choice -> setValidity(false);
        $manager->persist($choice);

        $manager->flush();

        return $this->redirectToRoute('all_questions');

    }

    /**
     * @Route("/Admin", name="all_questions")
     */
    public function showAllQuestion(QuestionsRepository $questionsRepository)
    {
        $questions = $questionsRepository->findAll();
        return $this->render('quizz/allQuestion.html.twig', [
            'questions'=>$questions,
        ]);
    }

    /**
     * @Route("/answer", name="add_new_answers")
     */
    public function answer(QuestionsRepository $questionsRepository, AnswersRepository $answersRepository,UsersRepository $usersRepository, ChoicesRepository $choicesRepository, Request $request, ObjectManager $manager)
    {
        $questions = $questionsRepository->findAll();

        $user = $usersRepository->find($request->get('user_id'));
        $request = $this->get('request_stack')->getCurrentRequest();
        dump($user);
        foreach($questions as $question){
            //dump($question->getId());   //load question by ID
            //dump($request->get('response_'.$question->getId())); // load response by ID


            $answer = new Answers();
            $answer -> setChoice($request->get('response_'.$question->getId()));
            $answer -> setUser($user);
            $chosenResponse = $request->get('response_'.$question->getId());

            $choice = $choicesRepository->find($chosenResponse);


            //dump($choice);


            $answer -> setResult($choice->getValidity());
            $manager->persist($answer);

            //dump($toto);
            /*$answer -> setChoice($toto);
            //$answer -> setChoice($request->get('response_'.$question->getId()));
            $answer -> setUser($user);
            $answer -> setResult(true);

            $manager->persist($answer);*/

        }
        $manager->flush();
        //$manager->flush();

        return $this->redirectToRoute('final_result', ['id'=> $user->getId()]);
        //return new Response('toto');
    }
        /**
         * @Route("/Result/{id}", name="final_result")
         */
        public function finalResult($id,UsersRepository $usersRepository, QuestionsRepository $questionsRepository, AnswersRepository $answersRepository)
    {
        $results = array(
            "ok"=>0,
            "ko"=>0,
            "total"=>0
        );
        $user = $usersRepository->find($id);
        //$choiceUser = $answersRepository->findBy(array("user_id"=>$user));
        $choicesUser = $answersRepository->findByUser($user);
        //dump($choiceUser);
        foreach($choicesUser as $choice){
            if($choice->getResult() == true){
                $results["ok"]++;
            }else{
                $results["ko"]++;
            }
            $results["total"]++;
        }
        return $this->render('quizz/final_result.html.twig', [
            'user'=>$user,
            'results'=>$results,
        ]);
    }


    /**
     * @Route("/delete/{question_id}", name="delete")
     */
    public function deleteQuestion($question_id,QuestionsRepository $questionsRepository,ChoicesRepository $choicesRepository, ObjectManager $manager)
    {
        $question = $questionsRepository->find($question_id);
        $choices = $choicesRepository->findByQuestion($question);
        foreach ($choices as $choice){
            $manager->remove($choice);
        }
        $manager->remove($question);
        $manager->flush();

        return $this->redirectToRoute('all_questions');
    }

//$userId not $id
//request manager a degager
// dans construtror

}