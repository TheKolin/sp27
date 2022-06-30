<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Lesson;
use App\Entity\Subject;
use App\Entity\User;
use App\Entity\UserStatus;
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

class AdminController extends AbstractController
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
     *      path="/admin",
     *      methods={"GET"},
     *      name="admin.dashboard",
     * )
     */
    public function adminDashboard(): Response {
        return $this->render('Admin/dashboard.html.twig', [
            
        ]);
    }

    /**
     * @Route(
     *      path="/admin/users",
     *      methods={"GET"},
     *      name="admin.users",
     * )
     */
    public function adminUsers(): Response {

        $groups = $this->groupRepository->findBy([], ['number' => 'ASC', 'subGroup' => 'ASC']);

        return $this->render('Admin/users.html.twig', [
            'groups' => $groups
        ]);
    }

    /**
     * @Route(
     *      path="/admin/ajax/users/get",
     *      methods={"GET"},
     *      name="admin.get.users",
     * )
     */
    public function adminGetUsers(Request $request): Response {

        $group = $request->query->get('group');
        
        if($group === UserType::TEACHER) {
            $criteria = [
                'userType' => UserType::TEACHER
            ];
        } else {
            $criteria = [
                'userType' => UserType::STUDENT,
                'class' => $group
            ];
        }
        
        $users = $this->userRepository->findBy($criteria);
        
        return $this->json($users);
    }

    /**
     * @Route(
     *      path="/admin/ajax/users/add",
     *      methods={"GET"},
     *      name="admin.add.users",
     * )
     */
    public function adminAddUsers(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response {

        $firstName = $request->query->get('firstName');
        $lastName = $request->query->get('lastName');
        $group = $request->query->get('group');

        $user = new User;
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        if($group !== UserType::TEACHER){
            $user->setClass($this->groupRepository->findOneBy(['id' => $group]));
            $user->setUserType($this->userTypeRepository->findOneBy(['code' => UserType::STUDENT]));
        } else {
            $user->setUserType($this->userTypeRepository->findOneBy(['code' => UserType::TEACHER]));
        }
        
        $user->setUserStatus($this->userStatusRepository->findOneBy(['code' => UserStatus::CREATED]));

        $user->setUuid(uniqid());
        $defaultPassword = uniqid();
        $user->setPassword($userPasswordHasher->hashPassword($user, $defaultPassword));
        $user->setDefaultPassword($defaultPassword);

        $this->manager->persist($user);
        $this->manager->flush();

        return $this->json(['message' => 'Użytkownik dodany']);
    }

    /**
     * @Route(
     *      path="/admin/ajax/delete/users",
     *      methods={"GET"},
     *      name="admin.delete.users",
     * )
     */
    public function adminDeleteUsers(Request $request): Response {

        $id = $request->query->get('id');

        $user = $this->userRepository->findOneBy(['id' => $id]);

        $this->manager->remove($user);
        $this->manager->flush();

        return $this->json(['message' => 'Usunięto użytkownika']);
    }

    /**
     * @Route(
     *      path="/admin/ajax/revoke/password",
     *      methods={"GET"},
     *      name="admin.revoke.password",
     * )
     */
    public function adminRevokeUserPassword(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response {

        $id = $request->query->get('id');

        $user = $this->userRepository->findOneBy(['id' => $id]);

        $defaultPassword = uniqid();
        $user->setPassword($userPasswordHasher->hashPassword($user, $defaultPassword));
        $user->setDefaultPassword($defaultPassword);

        $this->manager->persist($user);
        $this->manager->flush();

        return $this->json(['message' => 'Hasło zresetowane']);
    }

    /**
     * @Route(
     *      path="/admin/ajax/group/add",
     *      methods={"GET"},
     *      name="admin.add.group",
     * )
     */
    public function adminAddGroup(Request $request): Response {

        $number = $request->query->get('number');
        $subclass = $request->query->get('subclass');

        $findGroup = $this->groupRepository->findBy(['number' => $number, 'subGroup' => $subclass]);
        if($findGroup > 0){
            return $this->json(['message' => 'Taka klasa już istnieje']);
        }

        $group = new Group;
        $group->setNumber($number);
        $group->setSubGroup($subclass);

        $this->manager->persist($group);
        $this->manager->flush();

        return $this->json(['message' => 'Grupa dodana']);
    }

    /**
     * @Route(
     *      path="/admin/lessons",
     *      methods={"GET"},
     *      name="admin.lessons",
     * )
     */
    public function adminLessons(): Response {

        $subjects = $this->subjectRepository->findAll();
        $lessons = $this->lessonRepository->findAll();
        $teachers = $this->userRepository->findBy(['userType' => UserType::TEACHER]);
        $lessonTypes = $this->lessonTypeRepository->findAll();

        return $this->render('Admin/lessons.html.twig', [
            'subjects' => $subjects,
            'lessons' => $lessons,
            'teachers' => $teachers,
            'lessonTypes' => $lessonTypes
        ]);
    }

    /**
     * @Route(
     *      path="/admin/ajax/subject/add",
     *      methods={"GET"},
     *      name="admin.add.subject",
     * )
     */
    public function adminAddSubject(Request $request): Response {

        $name = $request->query->get('name');

        $findSubject = $this->subjectRepository->findBy(['name' => $name]);
        if($findSubject > 0){
            return $this->json(['message' => 'Taki przedmiot już istnieje']);
        }

        $subject = new Subject;
        $subject->setName($name);

        $this->manager->persist($subject);
        $this->manager->flush();

        return $this->json(['message' => 'Dodano przedmiot']);
    }

    /**
     * @Route(
     *      path="/admin/ajax/add/lessons",
     *      methods={"GET"},
     *      name="admin.add.lessons",
     * )
     */
    public function adminAddLessons(Request $request): Response {

        $subject = $request->query->get('subject');
        $teacher = $request->query->get('teacher');
        $lessonTypeCode = $request->query->get('lessonTypeCode');
        $dayOfWeek = $request->query->get('dayOfWeek');
        $time = $request->query->get('time');

        $lesson = new Lesson;
        $lesson->setSubject($this->subjectRepository->findOneBy(['id' => $subject]));
        $lesson->setTeacher($this->userRepository->findOneBy(['id' => $teacher]));
        $lesson->setLessonType($this->lessonTypeRepository->findOneBy(['code' => $lessonTypeCode]));
        $lesson->setDayOfWeek($dayOfWeek);
        $lesson->setTime($time);
        $lesson->setStudentsCount(0);

        $this->manager->persist($lesson);
        $this->manager->flush();

        return $this->json(['message' => 'Dodano lekcję']);
    }

     /**
     * @Route(
     *      path="/admin/ajax/delete/lessons",
     *      methods={"GET"},
     *      name="admin.delete.lessons",
     * )
     */
    public function adminDeleteLessons(Request $request): Response {

        $id = $request->query->get('id');

        $lesson = $this->lessonRepository->findOneBy(['id' => $id]);

        $this->manager->remove($lesson);
        $this->manager->flush();

        return $this->json(['message' => 'Usunięto lekcję']);
    }

    /**
     * @Route(
     *      path="/admin/reservations",
     *      methods={"GET"},
     *      name="admin.reservations",
     * )
     */
    public function adminReservations(): Response {

        $reservations = $this->reservationRepository->findAll();
        $subjects = $this->subjectRepository->findAll();
        $students = $this->userRepository->findBy(['userType' => UserType::STUDENT]);
        $reservationStatuses = $this->reservationStatusRepository->findAll();

        return $this->render('Admin/reservations.html.twig', [
            'subjects' => $subjects,
            'students' => $students,
            'reservationStatuses' => $reservationStatuses,
            'reservations' => $reservations
        ]);
    }

    /**
     * @Route(
     *      path="/admin/ajax/reservations/get",
     *      methods={"GET"},
     *      name="admin.get.reservations",
     * )
     */
    public function adminGetReservations(Request $request): Response {

        $student = $request->query->get('student');
        $status = $request->query->get('status');

        $criteria = [];
        if($student){
            $criteria = array_merge($criteria,[
                'student' => $student
            ]);
        }
        if($status){
            $criteria = array_merge($criteria,[
                'reservationStatus' => $status
            ]);
        }
        
        $reservations = $this->reservationRepository->findBy($criteria);
        
        return $this->json($reservations);
    }

     /**
     * @Route(
     *      path="/admin/ajax/delete/reservation",
     *      methods={"GET"},
     *      name="admin.delete.reservation",
     * )
     */
    public function adminDeleteReservation(Request $request): Response {

        $id = $request->query->get('id');

        $reservation = $this->reservationRepository->findOneBy(['id' => $id]);

        $this->manager->remove($reservation);
        $this->manager->flush();

        return $this->json(['message' => 'Usunięto rezerwację']);
    }
}
