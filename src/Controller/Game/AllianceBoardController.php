<?php

namespace EtoA\Controller\Game;

use EtoA\Admin\AllianceBoardAvatar;
use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Alliance\AllianceService;
use EtoA\Alliance\Board\AllianceBoardCategoryRankRepository;
use EtoA\Alliance\Board\AllianceBoardCategoryRepository;
use EtoA\Alliance\Board\AllianceBoardPostRepository;
use EtoA\Alliance\Board\AllianceBoardTopicRepository;
use EtoA\Entity\AllianceBoardCategory;
use EtoA\Entity\AllianceDiplomacy;
use EtoA\Image\ImageUtil;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class AllianceBoardController extends AbstractGameController
{
    public function __construct(
        private readonly AllianceRepository $allianceRepository,
        private readonly AllianceDiplomacyRepository $allianceDiplomacyRepository,
        private readonly UserRepository $userRepository,
        private readonly AllianceService $service,
        private readonly AllianceBoardCategoryRepository $allianceBoardCategoryRepository,
        private readonly AllianceBoardTopicRepository $allianceBoardTopicRepository,
        private readonly AllianceBoardCategoryRankRepository $allianceBoardCategoryRankRepository,
        private readonly AllianceRankRepository $allianceRankRepository,
        private readonly AllianceBoardPostRepository $allianceBoardPostRepository
    )
    {
    }

    #[Route('/game/allianceboard', name: 'game.alliance.allianceboard.overview')]
    public function allianceBoard(): Response {
        if(!$this->getUser()->getData()->getAlliance()->getId()) {
            return $this->redirectToRoute('game.alliance');
        }

        ob_start();

        $boardBulletDir = '/build/images/boardbullets';

        $alliance = $this->getUser()->getData()->getAlliance();
        $userAlliancePermission = $this->service->getUserAlliancePermissions($alliance, $this->getUser()->getData());
        $isAdmin = $userAlliancePermission->hasRights(AllianceRights::ALLIANCE_BOARD);

        $ranks = $this->allianceRankRepository->findBy(['alliance'=>$alliance->getId()]);
        $rank = array();
        foreach ($ranks as $r) {
            $rank[$r->getId()] = $r->getName();
        }

        $page = '';

        $categories = $this->allianceBoardCategoryRepository->findBy(['alliance'=>$alliance->getId()],['order'=>'DESC','name'=>'DESC']);
        if ($categories) {
            $categoryIds = array_map(fn (AllianceBoardCategory $category) => $category->getId(), $categories);
            $postCounts = $this->allianceBoardTopicRepository->getTopicPostCountsByCategory($categoryIds);
            $topicCounts = $this->allianceBoardTopicRepository->getCategoryTopicCounts($categoryIds);

            echo '<table class="tb">';
            echo "<tr><th colspan=\"2\">Kategorie</th><th>Posts</th><th>Topics</th><th>Letzter Beitrag</th>";
            if ($isAdmin) {
                echo "<th style=\"width:50px;\">Aktionen</th>";
            }
            echo "</tr>";
            $accessCnt = 0;
            foreach ($categories as $category) {
                if ($isAdmin || isset($myCat[$category->getId()])) {
                    $accessCnt++;

                    $post = $this->allianceBoardTopicRepository->getTopicWithLatestPost($category->getId());
                    $topic = $post?->getTopic();

                    if ($topic !== null) {
                        $ps = "<a href=\"?page=$page&amp;topic=" . $topic->getId() . "#" . $post->getId() . "\" " . tm($topic->getSubject() . ", " . StringUtils::formatDate($topic->getTimestamp()), "Geschrieben von: <b>" . $post->getUserNick() . "</b>") . ">" . $topic->getSubject() . "<br/>" . StringUtils::formatDate($topic->getTimestamp()) . "</a>";
                    } else
                        $ps = "-";
                    echo "<tr>";
                    if (!$category->getBullet()|| !is_file($this->getParameter('kernel.project_dir').'/public'.$boardBulletDir . "/" . $category->getBullet())) $category->setBullet(AllianceBoardAvatar::DEFAULT_IMAGE);
                    echo "<td style=\"width:40px;vertical-align:middle;\">
                                    <a href=\"?page=$page&amp;bnd=0&cat=" . $category->getId() . "\">
                                        <img src=\"" . $boardBulletDir . "/" . $category->getBullet() . "\" style=\"width:40px;height:40px;\" />
                                    </a>
                                </td>";
                    echo "<td style=\"width:300px;\"";
                    if ($isAdmin) {
                        $rstr = "";
                        $categoryRankIds = $this->allianceBoardCategoryRankRepository->getRanksForCategories($category->getId());
                        foreach ($rank as $k => $v) {
                            if (in_array($k, $categoryRankIds, true)) {
                                $rstr .= $v . ", ";
                            }
                        }

                        if ($rstr != "") $rstr = substr($rstr, 0, strlen($rstr) - 2);
                        echo " " . tm("Admin-Info: " . $category->getName(), "<b>Position:</b> " . $category->getOrder() . "<br/><b>Zugriff:</b> " . $rstr) . "";
                    }
                    echo ">
                                <b><a href=\"" . $this->generateUrl('game.alliance.allianceboard.showtopics',['id'=>$category->getId()]) . "\">" . ($category->getName() != "" ? $category->getName() : "Unbenannt") . "</a></b>
                                <br/>" . BBCodeUtils::toHTML($category->getDescription()) . "</td>";
                    echo "<td>" . $postCounts[$category->getId()] . "</td>";
                    echo "<td>" . $topicCounts[$category->getId()] . "</td>";
                    echo "<td>$ps</td>";
                    if ($isAdmin) {
                        echo "<td style=\"vertical-align:middle;text-align:center;\">
                                        <a href=\"" . $this->generateUrl('game.alliance.allianceboard.editcategory',['id'=>$category->getId()]) . "\">" . ImageUtil::icon('edit') . "</a>
                                        <a href=\"" . $this->generateUrl('game.alliance.allianceboard.deletecategory',['id'=>$category->getId()]) . "\">" . ImageUtil::icon('delete') . "</a>
                                    </td>";
                    }
                    echo "</tr>";
                }
            }
            if ($accessCnt == 0)
                echo "<tr><td colspan=\"5\"><i>Du hast zu keiner Kategorie Zugriff!</i></td></tr>";
            echo "</table>";
        } else {
            echo '<div class="boxLayout error">';
            echo '<div class="infoboxtitle"><span>Fehler</span></div>';
            echo '<div class="infoboxcontent">Keine Kategorien vorhanden!</div></div>';
        }

        if ($isAdmin)
            echo "<br/> <a href='".$this->generateUrl('game.alliance.allianceboard.newcategory')."'><input type=\"button\" value=\"Neue Kategorie erstellen\"/></a> &nbsp; ";
        echo "<a href='".$this->generateUrl('game.alliance.overview') ."'><input type=\"button\" value=\"Zur Allianzseite\"/></a><br/><br/>";

        //shows Bnd forums
        $diplomacies = $this->allianceDiplomacyRepository->getDiplomacies($alliance->getId(), AllianceDiplomacyLevel::BND_CONFIRMED);
        if (count($diplomacies) > 0) {
            $allianceBndIds = array_map(fn (AllianceDiplomacy $diplomacy) => $diplomacy->getId(), $diplomacies);
            $topicCounts = $this->allianceBoardTopicRepository->getBndTopicCounts($allianceBndIds);
            $postCounts = $this->allianceBoardTopicRepository->getBndPostCounts($allianceBndIds);

            echo "<table>";
            echo "<tr><th colspan=\"2\">Bündnisforen</th><th>Posts</th><th>Topics</th><th>Letzer Beitrag</th>";
            if ($isAdmin) {
                echo "<th>Aktionen</th>";
            }
            echo "</tr>";
            $accessCnt = 0;
            foreach ($diplomacies as $diplomacy) {
                if ($isAdmin || isset($myCat[$diplomacy->getId()])) {// @ todo
                    $accessCnt++;
                    $topic = $this->allianceBoardTopicRepository->getTopicWithLatestPost(0, $diplomacy->getId());
                    if ($topic !== null) {
                        $ps = "<a href=\"?page=$page&amp;topic=" . $topic->getId() . "#" . $topic->post->getId() . "\" " . tm($topic->getSubject() . ", " . StringUtils::formatDate($topic->getTimestamp()), "Geschrieben von: <b>" . $topic->post->getUserNick() . "</b>") . ">" . $topic->getSubject() . "<br/>" . StringUtils::formatDate($topic->getTimestamp()) . "</a>"; //ToDo User auch von anderen Allianzen
                    } else
                        $ps = "-";
                    echo "<tr>";
                    echo "<td style=\"width:40px;\"><img src=/build/\"" . $boardBulletDir . "/" . AllianceBoardAvatar::DEFAULT_IMAGE . "\" style=\"width:40px;height:40px;\" /></td>";
                    echo "<td style=\"width:300px;\"";
                    if ($isAdmin) {
                        $rstr = "";
                        $bndRankIds = $this->allianceBoardCategoryRankRepository->getRanksForBnd($diplomacy->getId());
                        foreach ($rank as $k => $v) {
                            if (in_array($k, $bndRankIds, true)) {
                                $rstr .= $v . ", ";
                            }
                        }

                        if ($rstr != "") $rstr = substr($rstr, 0, strlen($rstr) - 2);
                        echo " " . tm("Admin-Info: " . stripslashes($diplomacy->getAlliance2()->toString()),/*"<b>Position:</b> ".$arr['cat_order']."<br/>*/ "<b>Zugriff:</b> " . $rstr) . "";
                    }
                    echo "><b><a href=\"?page=$page&amp;cat=0&bnd=" . $diplomacy->getId() . "\"";
                    echo ">" . stripslashes($diplomacy->getAlliance2()->toString()) . "</a></b><br/>" . BBCodeUtils::toHTML($diplomacy->getText()) . "</td>";
                    echo "<td>" . $postCounts[$diplomacy->getId()] . "</td>";
                    echo "<td>" . $topicCounts[$diplomacy->getId()] . "</td>";
                    echo "<td>$ps</td>";
                    if ($isAdmin) {
                        echo "<td style=\"width:90px;\"><input type=\"button\" value=\"Bearbeiten\" onclick=\"document.location='?page=$page&editbnd=" . $diplomacy->getId() . "'\" /><br/>
                                    </td>";
                    }
                    echo "</tr>";
                }
            }
            if ($accessCnt == 0)
                echo "<tr><td colspan=\"5\"><i>Du hast zu keiner Kategorie Zugriff!</i></td></tr>";
            echo '</table>';
        }

        return $this->render('game/alliance/allianceboard/allianceboard_overview.html.twig',[
            'overview' => ob_get_clean()
        ]);
    }

    #[Route('/game/allianceboard/newcategory', name: 'game.alliance.allianceboard.newcategory')]
    public function newCategory(Request $request): Response {
        $form = $this->buildCategoryForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cid = $this->allianceBoardCategoryRepository->addCategory($form->getData()['catName'], $form->getData()['catDesc'], $form->getData()['catOrder'], $form->getData()['catBullet'], $this->getUser()->getData()->getAlliance()->getId());
            $newRanks = array_map(fn ($value) => (int) $value, $form->getData()['rank'] ?? []);
            $this->allianceBoardCategoryRankRepository->replaceRanks($cid, 0, $newRanks);
            $msg['success'] = "Neue Kategorie gespeichert!";
        }

        return $this->render('game/alliance/allianceboard/allianceboard_newcategory.html.twig',[
            'form' => $form,
            'msg' => $msg??null
        ]);
    }

    // Delete a forum category and all it's content
    //TODO: use entity
    #[Route('/game/allianceboard/deletecategory/{id}', name: 'game.alliance.allianceboard.deletecategory')]
    public function deleteCategory(Request $request, int $id): Response {
        if($this->isAdmin()) {
            $category = $this->allianceBoardCategoryRepository->getCategory($id, $this->getUser()->getData()->getAlliance()->getId());
            if($category) {
                $form = $this->createFormBuilder()
                    ->add('catDelete', SubmitType::class, [
                        'label' => 'Löschen',
                        'attr' => [
                            'onclick' => "return confirm('Willst du die Kategorie " . $category->getName() . " wirklich löschen?')"
                        ]
                    ])
                    ->getForm();

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $this->allianceBoardCategoryRepository->deleteCategory($id, $this->getUser()->getData()->getAlliance()->getId());
                    $msg['success'] = "Kategorie gelöscht!";
                }

            } else {
                return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                    'message' => 'Datensatz nicht gefunden!'
                ]);
            }

            return $this->render('game/alliance/allianceboard/allianceboard_deletecategory.html.twig',[
                'form' => $form,
                'msg' => $msg??null,
                'categoryName' => $category?->getName()
            ]);
        }
        return $this->redirectToRoute('game.alliance.allianceboard.overview');
    }

    // Edit a category
    //TODO: use entity
    #[Route('/game/allianceboard/editcategory/{id}', name: 'game.alliance.allianceboard.editcategory')]
    public function editCategory(Request $request, int $id): Response {
        if(!$this->isAdmin()) {
            return $this->redirectToRoute('game.alliance.allianceboard.overview');
        }

        $category = $this->allianceBoardCategoryRepository->getCategory($id, $this->getUser()->getData()->getAlliance()->getId());

        if($category) {
            $form = $this->buildCategoryForm($category);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->allianceBoardCategoryRepository->updateCategory($id, $category->getName(), $category->getDescription(), $category->getOrder(), $category->getBullet(),$this->getUser()->getData()->getAlliance()->getId());
                $newRanks = array_map(fn ($value) => (int) $value, $form->get('rank')->getData() ?? []);
                $this->allianceBoardCategoryRankRepository->replaceRanks($id, 0, $newRanks);
                $msg['success'] = "Änderungen gespeichert!";
            }

            return $this->render('game/alliance/allianceboard/allianceboard_editcategory.html.twig',[
                'form' => $form,
                'msg' => $msg??null
            ]);
        } else {
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Datensatz nicht gefunden!'
            ]);
        }
    }

    // Show topics in category
    //TODO: use entity
    #[Route('/game/allianceboard/showtopics/{id}', name: 'game.alliance.allianceboard.showtopics')]
    public function showTopics(int $id): Response {
        $myCat = [];

        $cu = $this->getUser()->getData();
        $allianceCategories = $this->allianceBoardCategoryRepository->getCategories($cu->getAlliance()->getId());
        $availableCategories = $this->allianceBoardCategoryRankRepository->getCategoriesForRank($cu->getAlliance()->getId(), $cu->getAllianceRankId());
        if (count($allianceCategories) > 0) {
            foreach ($allianceCategories as $category) {
                $myCat[$category->getId()] = in_array($category->getId(), $availableCategories, true);
            }
        }
        $allianceUsers = $this->userRepository->searchUsers(UserSearch::create()->allianceId($cu->getAlliance()->getId()));

        if ($this->isAdmin() || (isset($myCat[$id]) && $myCat[$id])) {
            $category = $this->allianceBoardCategoryRepository->getCategory($id, $cu->getAlliance()->getId());
            if (!$category) {
                return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                    'message' => 'Kategorie existiert nicht!'
                ]);
            }
        } else
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Kein Zugriff!'
            ]);

        return $this->render('game/alliance/allianceboard/allianceboard_topics.html.twig',[
            'allianceBoardTopicRepository' => $this->allianceBoardTopicRepository,
            'category' => $category,
            'isAdmin' => $this->isAdmin(),
            'allianceUsers' => $allianceUsers,
            'allianceBoardPostRepository' => $this->allianceBoardPostRepository
        ]);
    }

    //create new topic
    //TODO: use entity
    #[Route('/game/allianceboard/newtopic/{id}', name: 'game.alliance.allianceboard.newtopic')]
    public function newTopic(int $id, Request $request): Response {
        $category = $this->allianceBoardCategoryRepository->getCategory($id, $this->getUser()->getData()->getAlliance()->getId());

        if(!$category)
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Diese Kategorie existiert nicht!'
            ]);

        $form = $this->createFormBuilder()
            ->add('topicSubject', TextType::class,
                [
                    'attr' => ['size'=>40],
                    'constraints'=> new NotBlank([
                        'message' => 'Du musst einen Text eingeben!',
                    ]),
                ]
            )
            ->add('postText', TextareaType::class,
                [
                    'attr' => [
                        'rows'=>6,
                        'cols' =>80
                    ],
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Speichern'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cu = $this->getUser()->getData();
            $mid = $this->allianceBoardTopicRepository->addTopic($form->getData()['topicSubject'], 0, $category->getId(), $cu->getId(), $cu->getNick());
            $this->allianceBoardPostRepository->addPost($mid, $form->getData()['postText'], $cu->getId(), $cu->getNick());
            return new RedirectResponse($this->generateUrl('game.alliance.allianceboard.showtopics',['id'=>$id]));
        }

        return $this->render('game/alliance/allianceboard/allianceboard_newtopic.html.twig',[
            'category' => $category,
            'form' => $form
        ]);
    }

    //edit topic
    //TODO: use entity
    #[Route('/game/allianceboard/edittopic/{id}', name: 'game.alliance.allianceboard.edittopic')]
    public function editTopic(int $id, Request $request): Response {
        $topic = $this->allianceBoardTopicRepository->getTopic($id);

        if (!$topic) {
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Datensatz nicht gefunden!'
            ]);
        }

        if ($this->getUser()->getId() == $topic->getUserId() || $this->isAdmin()) {
            $form = $this->createFormBuilder($topic)
                ->add('topicSubject', TextType::class,
                    [
                        'attr' => ['size'=>40],
                        'constraints'=> new NotBlank([
                            'message' => 'Du musst einen Text eingeben!',
                        ]),
                    ]
                )
                ->add('topicTop', CheckboxType::class,[
                    'required' => false
                ])
                ->add('topicClosed', CheckboxType::class,[
                    'required' => false
                ])
                ->add('topicCatId', ChoiceType::class, [
                    'choices'=>$this->allianceBoardCategoryRepository->getCategories($this->getUser()->getData()->getAlliance()->getId()),
                    'choice_value' => 'id',
                    'choice_label' => function (?AllianceBoardCategory $category): string {
                        return $category ? strtoupper($category->getName()) : '';
                    },
                    'mapped' => false
                ])
                ->add('topicEdit', SubmitType::class, ['label' => 'Speichern'])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->allianceBoardTopicRepository->updateTopic($id, $topic->getTopicSubject(), 0, $form->get('topicCatId')->getData()->id, $topic->isTop(), $topic->isClosed());
                $msg = "Änderungen gespeichert!";
            }

            return $this->render('game/alliance/allianceboard/allianceboard_edittopic.html.twig',[
                'msg' => $msg??null,
                'form' => $form,
                'isAdmin' => $this->isAdmin()
            ]);
        } else {
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Keine Berechtigung!'
            ]);
        }
    }

    //delete topic
    #[Route('/game/allianceboard/deletetopic/{id}', name: 'game.alliance.allianceboard.deletetopic')]
    public function deleteTopic(int $id, Request $request): Response
    {
        $topic = $this->allianceBoardTopicRepository->getTopic($id);

        if(!$topic) {
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Datensatz nicht gefunden!'
            ]);
        }

        if(!$this->isAdmin()) {
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Keine Berechtigung!'
            ]);
        }

        $form = $this->createFormBuilder()
           ->add('topicDelete', SubmitType::class, ['label' => 'Löschen'])
           ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->allianceBoardTopicRepository->deleteTopic($id);
            $msg['success'] = "Thema gelöscht!";
        }

        return $this->render('game/alliance/allianceboard/allianceboard_deletetopic.html.twig',[
            'topicName' => $topic->getSubject(),
            'msg' => $msg??null,
            'form' => $form
        ]);
    }

    //show posts
    #[Route('/game/allianceboard/posts/{id}', name: 'game.alliance.allianceboard.showposts')]
    public function showPosts(int $id, Request $request): Response
    {
        $posts = $this->allianceBoardPostRepository->getPosts($id);
        $topic = $this->allianceBoardTopicRepository->getTopic($id);

        if($posts) {
            return $this->render('game/alliance/allianceboard/allianceboard_posts.html.twig',[
                'posts' => $posts,
                'userRepository' => $this->userRepository,
                'topic' => $topic,
                'cpost' => $this->allianceBoardPostRepository->getUserAlliancePostCounts($this->getUser()->getData()->getAlliance()->getId(), $this->getUser()->getId()),
                'isAdmin' => $this->isAdmin(),
                'category' => $this->allianceBoardCategoryRepository->getCategory($topic->getCategory()->getId() , $this->getUser()->getData()->getAlliance()->getId())
            ]);
        } else {
            if ($topic) {
                $this->allianceBoardTopicRepository->deleteTopic($id);
            }
            return new RedirectResponse($this->generateUrl('game.alliance.allianceboard.overview'));
        }
    }

    //create new post
    #[Route('/game/allianceboard/newpost/{id}', name: 'game.alliance.allianceboard.newpost')]
    public function newPost(int $id, Request $request): Response
    {
        $topic = $this->allianceBoardTopicRepository->getTopic($id);

        if(!$topic)
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Dieses Thema existiert nicht!'
            ]);

        if($topic->isClosed())
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Dieses Thema ist geschlossen!'
            ]);


        $form = $this->createFormBuilder()
            ->add('submit', SubmitType::class, ['label' => 'Beitrag speichern'])
            ->add('postText', TextareaType::class,
                [
                    'attr' => [
                        'rows'=>10,
                        'cols' =>90
                    ],
                    'constraints'=> new NotBlank([
                        'message' => 'Du musst einen Text eingeben!',
                    ]),
                ]
            )
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->allianceBoardPostRepository->addPost($id, $form->getData()['postText'], $this->getUser()->getId(), $this->getUser()->getUserIdentifier());
            $this->allianceBoardTopicRepository->updateTopicTimestamp($id);
            return new RedirectResponse($this->generateUrl('game.alliance.allianceboard.showposts',['id'=>$topic->getId()]));
        }

        return $this->render('game/alliance/allianceboard/allianceboard_newpost.html.twig',[
            'msg' => $msg??null,
            'form' => $form,
            'id' => $topic->getCategory()->getId()
        ]);
    }

    //edit post
    #[Route('/game/allianceboard/editpost/{id}', name: 'game.alliance.allianceboard.editpost')]
    public function editPost(int $id, Request $request): Response
    {
        $post = $this->allianceBoardPostRepository->getPost($id);

        if(!$post)
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Datensatz nicht gefunden!'
            ]);

        if($this->getUser()->getId() == $post->getUserId() || $this->isAdmin()) {
            $form = $this->createFormBuilder($post)
                ->add('postEdit', SubmitType::class, ['label' => 'Speichern'])
                ->add('postText', TextareaType::class,
                    [
                        'attr' => [
                            'rows'=>10,
                            'cols' =>90
                        ],
                        'constraints'=> new NotBlank([
                            'message' => 'Du musst einen Text eingeben!',
                        ]),
                    ]
                )
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($this->isAdmin())
                    $this->allianceBoardPostRepository->updatePost($id, $post->getText());
                else
                    $this->allianceBoardPostRepository->updatePost($id, $post->getText(), $this->getUser()->getId());
                $msg['success'] = "Änderungen gespeichert!";
            }

            return $this->render('game/alliance/allianceboard/allianceboard_editpost.html.twig',[
                'msg' => $msg??null,
                'form' => $form,
            ]);
        }
        else {
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Keine Berechtigung!'
            ]);
        }
    }

    //delete post
    #[Route('/game/allianceboard/deletepost/{id}', name: 'game.alliance.allianceboard.deletepost')]
    public function deletePost(int $id, Request $request): Response
    {
        $post = $this->allianceBoardPostRepository->getPost($id);

        if(!$post) {
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Datensatz nicht gefunden!'
            ]);
        }

        if($this->getUser()->getId() == $post->getUserId() || $this->isAdmin()) {
            $form = $this->createFormBuilder()
                ->add('postDelete', SubmitType::class, ['label' => 'Löschen'])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->allianceBoardPostRepository->deletePost($id);
                $msg['success'] = "Post gelöscht!";
            }

            return $this->render('game/alliance/allianceboard/allianceboard_deletepost.html.twig',[
                'post' => $post,
                'msg' => $msg??null,
                'form' => $form
            ]);
        }
        else {
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Keine Berechtigung!'
            ]);
        }
    }

    private function isAdmin():bool {
        $userAlliancePermission = $this->service->getUserAlliancePermissions($this->getUser()->getData()->getAlliance(),$this->getUser()->getData());
        return $userAlliancePermission->hasRights(AllianceRights::ALLIANCE_BOARD);
    }

    private function buildCategoryForm(mixed $data = null):FormInterface
    {
        $alliance = $this->allianceRepository->getAlliance($this->getUser()->getData()->getAlliance()->getId());
        $ranks = $this->allianceRankRepository->getRanks($alliance->getId());
        $rank = array();
        foreach ($ranks as $r) {
            $rank[$r->getId()] = $r->getName();
        }

        $boardBulletDir = 'build/images/boardbullets';

        $d = opendir($boardBulletDir);
        $bullets = array();
        while ($f = readdir($d)) {
            if (is_file($boardBulletDir . "/" . $f) && !is_dir($boardBulletDir . "/" . $f) && $f != AllianceBoardAvatar::DEFAULT_IMAGE) {
                $bullets[$f] = $f;
            }
        }

        return $this->createFormBuilder($data)
            ->add('catName', TextType::class,
                [
                    'attr' => ['size'=>40],
                    'constraints'=> new NotBlank([
                        'message' => 'Du musst einen Text eingeben!',
                    ]),
                ]
            )
            ->add('catDesc', TextType::class, [
                'attr' => ['size'=>40],
                'required' => false
            ])
            ->add('catOrder', NumberType::class, [
                'attr' => [
                    'maxlength'=>40,'size'=>1
                ],
                'scale' => 0,
                'constraints'=> new Type([
                    'type' => 'integer',
                    'message' => 'Du musst eine Zahl eingeben!',
                ]),
            ])
            ->add('rank', ChoiceType::class, [
                'choices'=>array_flip($rank),
                'expanded'=>true,
                'multiple' =>true,
                'mapped' => false
            ])
            ->add('catBullet', ChoiceType::class, [
                'choices'=>array_flip($bullets),
                'placeholder' => 'Standard-Symbol',
                'placeholder_attr' => ['value'=>AllianceBoardAvatar::DEFAULT_IMAGE],
                'attr'=>['data-model'=>"on(change)|image"]
            ])
            ->add('catSave', SubmitType::class, ['label' => 'Kategorie speichern'])
            ->getForm();
    }
}