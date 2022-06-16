<?php

namespace App\Controller;

use App\Entity\LessonType;
use App\Entity\Reservation;
use App\Entity\ReservationStatus;
use App\Entity\UserType;
use App\Repository\GroupRepository;
use App\Repository\LessonRepository;
use App\Repository\LessonTypeRepository;
use App\Repository\ReservationRepository;
use App\Repository\ReservationStatusRepository;
use App\Repository\SubjectRepository;
use App\Repository\UserRepository;
use App\Repository\UserStatusRepository;
use App\Repository\UserTypeRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        GroupRepository $groupRepository,
        LessonRepository $lessonRepository,
        LessonTypeRepository $lessonTypeRepository,
        ReservationRepository $reservationRepository,
        ReservationStatusRepository $reservationStatusRepository,
        SubjectRepository $subjectRepository,
        UserRepository $userRepository,
        UserStatusRepository $userStatusRepository,
        UserTypeRepository $userTypeRepository,
        ManagerRegistry $managerRegistry
    ){
        $this->groupRepository = $groupRepository;
        $this->lessonRepository = $lessonRepository;
        $this->lessonTypeRepository = $lessonTypeRepository;
        $this->reservationRepository = $reservationRepository;
        $this->reservationStatusRepository = $reservationStatusRepository;
        $this->subjectRepository = $subjectRepository;
        $this->userRepository = $userRepository;
        $this->userStatusRepository = $userStatusRepository;
        $this->userTypeRepository = $userTypeRepository;
        $this->manager = $managerRegistry->getManager();
    }

    /**
     * @Route(
     *      path="/dashboard",
     *      methods={"GET"},
     *      name="dashboard",
     * )
     */
    public function home(): Response {

        if($this->getUser()->getDefaultPassword()){
            return $this->redirectToRoute('security.password');
        }

        if($this->getUser()->getUserType()->getCode() == UserType::STUDENT){
            $choosableLessons = $this->lessonRepository->findBy(['lessonType' => LessonType::CHOOSABLE], ['time' => 'ASC']);
            $fixedLessons = $this->lessonRepository->findBy(['lessonType' => LessonType::FIXED], ['time' => 'ASC']);
            $reservations = $this->reservationRepository->findBy(['student' => $this->getUser()->getId()]);

            return $this->render('User/dashboard.html.twig', [
                'choosableLessons' => $choosableLessons,
                'fixedLessons' => $fixedLessons,
                'reservations' => $reservations
            ]);
        }

        if($this->getUser()->getUserType()->getCode() == UserType::TEACHER){
            $lessons = $this->lessonRepository->findBy(['teacher' => $this->getUser()->getId()], ['dayOfWeek' => 'ASC', 'time' => 'ASC']);

            return $this->render('User/dashboard.html.twig', [
                'lessons' => $lessons
            ]);
        }
    }

    /**
     * @Route(
     *      path="/teacher/ajax/get/reservation",
     *      methods={"GET"},
     *      name="teacher.get.reservations",
     * )
     */
    public function teacherGetReservations(Request $request): Response {
        $lesson = $request->query->get('lesson');
        
        $reservations = $this->reservationRepository->findBy(['lesson' => $lesson]);

        return $this->json($reservations);
    }

    /**
     * @Route(
     *      path="/teacher/ajax/set/status",
     *      methods={"GET"},
     *      name="teacher.set.status",
     * )
     */
    public function teacherSetStatus(Request $request): Response {
        $students = $request->query->all();
        
        foreach($students as $reservation => $status){
            if($status){
                $reservationStatus = ReservationStatus::DONE;
            } else {
                $reservationStatus = ReservationStatus::UNDONE;
            }

            $reservationStatus = $this->reservationStatusRepository->findOneBy(['code' => $reservationStatus]);
            $reservation = $this->reservationRepository->findOneBy(['id' => $reservation]);
            $reservation->setReservationStatus($reservationStatus);

            $this->manager->persist($reservation);
            $this->manager->flush();
        }

        return $this->json(['message' => 'Zapisano']);
    }

    /**
     * @Route(
     *      path="/student/ajax/add/reservation",
     *      methods={"GET"},
     *      name="student.add.reservation",
     * )
     */
    public function studentAddReservation(Request $request): Response {

        $comaSeparatedLessons = $request->query->get('lessons');
        $lessons = explode(',', $comaSeparatedLessons);

        foreach($lessons as $lesson){
            $selectedLesson = $this->lessonRepository->findOneBy(['id' => $lesson]);

            $reservation = new Reservation;
            $reservation->setStudent($this->getUser());
            $reservation->setLesson($selectedLesson);
            $reservation->setReservationStatus($this->reservationStatusRepository->findOneBy(['code' => ReservationStatus::CREATED]));

            $this->manager->persist($reservation);
            $this->manager->flush();
        }

        return $this->json(['message' => 'Zarezerwowano']);
    }

     /**
     * @Route(
     *      path="/security/password",
     *      methods={"GET"},
     *      name="security.password",
     * )
     */
    public function securityPassword(): Response {
        return $this->render('security/password.html.twig', [

        ]);
    }

    /**
     * @Route(
     *      path="/user/password",
     *      methods={"GET"},
     *      name="user.password",
     * )
     */
    public function userPassword(): Response {
        return $this->render('User/password.html.twig', [

        ]);
    }

    /**
     * @Route(
     *      path="/user/ajax/password",
     *      methods={"GET"},
     *      name="user.change.password",
     * )
     */
    public function userChangePassword(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response {
        $oldPasswd = $request->query->get('oldPasswd');
        $newPasswd = $request->query->get('newPasswd');
        $confirmPasswd = $request->query->get('confirmPasswd');

        $user = $this->getUser();
        $userPassword = $user->getPassword();
        $oldPasswd = $userPasswordHasher->hashPassword($user, $oldPasswd);

        if(!$oldPasswd){
            if($userPassword !== $oldPasswd){
                return $this->json(['message' => 'Niepoprawne hasło']);
            }
        }

        if($newPasswd !== $confirmPasswd){
            return $this->json(['message' => 'Podaj dwa identyczne hasła']);
        }

        $user->setPassword($userPasswordHasher->hashPassword($user, $newPasswd));
        if($user->getDefaultPassword()){
            $user->setDefaultPassword(NULL);
        }
        $this->manager->persist($user);
        $this->manager->flush();

        return $this->json(['message' => 'Hasło zmienione']);
    }
}
