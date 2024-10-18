<?php

namespace EtoA\Controller\Game;

use EtoA\Admin\AllianceBoardAvatar;
use EtoA\Alliance\AllianceDiplomacy;
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
use EtoA\Alliance\Board\Category;
use EtoA\Image\ImageUtil;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        if(!$this->getUser()->getData()->getAllianceId()) {
            return $this->redirectToRoute('game.alliance');
        }

        ob_start();

        $boardBulletDir = '/build/images/boardbullets';

        $alliance = $this->allianceRepository->getAlliance($this->getUser()->getData()->getAllianceId());
        $userAlliancePermission = $this->service->getUserAlliancePermissions($alliance, $this->getUser()->getData());
        $isAdmin = $userAlliancePermission->hasRights(AllianceRights::ALLIANCE_BOARD);

        $ranks = $this->allianceRankRepository->getRanks($alliance->id);
        $rank = array();
        foreach ($ranks as $r) {
            $rank[$r->id] = $r->name;
        }

        $page = '';

        $categories = $this->allianceBoardCategoryRepository->getCategories($alliance->id);
        if ($categories) {
            $categoryIds = array_map(fn (Category $category) => $category->id, $categories);
            $postCounts = $this->allianceBoardCategoryRepository->getCategoryPostCounts($categoryIds);
            $topicCounts = $this->allianceBoardCategoryRepository->getCategoryTopicCounts($categoryIds);

            echo '<table class="tb">';
            echo "<tr><th colspan=\"2\">Kategorie</th><th>Posts</th><th>Topics</th><th>Letzter Beitrag</th>";
            if ($isAdmin) {
                echo "<th style=\"width:50px;\">Aktionen</th>";
            }
            echo "</tr>";
            $accessCnt = 0;
            foreach ($categories as $category) {
                if ($isAdmin || isset($myCat[$category->id])) {
                    $accessCnt++;
                    $topic = $this->allianceBoardTopicRepository->getTopicWithLatestPost($category->id);
                    if ($topic !== null) {
                        $ps = "<a href=\"?page=$page&amp;topic=" . $topic->id . "#" . $topic->post->id . "\" " . tm($topic->subject . ", " . StringUtils::formatDate($topic->timestamp), "Geschrieben von: <b>" . $topic->post->userNick . "</b>") . ">" . $topic->subject . "<br/>" . StringUtils::formatDate($topic->timestamp) . "</a>";
                    } else
                        $ps = "-";
                    echo "<tr>";
                    if (!$category->bullet|| !is_file($this->getParameter('kernel.project_dir').'/public'.$boardBulletDir . "/" . $category->bullet)) $category->bullet = AllianceBoardAvatar::DEFAULT_IMAGE;
                    echo "<td style=\"width:40px;vertical-align:middle;\">
                                    <a href=\"?page=$page&amp;bnd=0&cat=" . $category->id . "\">
                                        <img src=\"" . $boardBulletDir . "/" . $category->bullet . "\" style=\"width:40px;height:40px;\" />
                                    </a>
                                </td>";
                    echo "<td style=\"width:300px;\"";
                    if ($isAdmin) {
                        $rstr = "";
                        $categoryRankIds = $this->allianceBoardCategoryRankRepository->getRanksForCategories($category->id);
                        foreach ($rank as $k => $v) {
                            if (in_array($k, $categoryRankIds, true)) {
                                $rstr .= $v . ", ";
                            }
                        }

                        if ($rstr != "") $rstr = substr($rstr, 0, strlen($rstr) - 2);
                        echo " " . tm("Admin-Info: " . $category->name, "<b>Position:</b> " . $category->order . "<br/><b>Zugriff:</b> " . $rstr) . "";
                    }
                    echo ">
                                <b><a href=\"" . $this->generateUrl('game.alliance.allianceboard.showtopics',['id'=>$category->id]) . "\">" . ($category->name != "" ? $category->name : "Unbenannt") . "</a></b>
                                <br/>" . BBCodeUtils::toHTML($category->description) . "</td>";
                    echo "<td>" . $postCounts[$category->id] . "</td>";
                    echo "<td>" . $topicCounts[$category->id] . "</td>";
                    echo "<td>$ps</td>";
                    if ($isAdmin) {
                        echo "<td style=\"vertical-align:middle;text-align:center;\">
                                        <a href=\"" . $this->generateUrl('game.alliance.allianceboard.editcategory',['id'=>$category->id]) . "\">" . ImageUtil::icon('edit') . "</a>
                                        <a href=\"" . $this->generateUrl('game.alliance.allianceboard.deletecategory',['id'=>$category->id]) . "\">" . ImageUtil::icon('delete') . "</a>
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
        $diplomacies = $this->allianceDiplomacyRepository->getDiplomacies($alliance->id, AllianceDiplomacyLevel::BND_CONFIRMED);
        if (count($diplomacies) > 0) {
            $allianceBndIds = array_map(fn (AllianceDiplomacy $diplomacy) => $diplomacy->id, $diplomacies);
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
                if ($isAdmin || isset($myCat[$diplomacy->id])) {// @ todo
                    $accessCnt++;
                    $topic = $this->allianceBoardTopicRepository->getTopicWithLatestPost(0, $diplomacy->id);
                    if ($topic !== null) {
                        $ps = "<a href=\"?page=$page&amp;topic=" . $topic->id . "#" . $topic->post->id . "\" " . tm($topic->subject . ", " . StringUtils::formatDate($topic->timestamp), "Geschrieben von: <b>" . $topic->post->userNick . "</b>") . ">" . $topic->subject . "<br/>" . StringUtils::formatDate($topic->timestamp) . "</a>"; //ToDo User auch von anderen Allianzen
                    } else
                        $ps = "-";
                    echo "<tr>";
                    echo "<td style=\"width:40px;\"><img src=/build/\"" . $boardBulletDir . "/" . AllianceBoardAvatar::DEFAULT_IMAGE . "\" style=\"width:40px;height:40px;\" /></td>";
                    echo "<td style=\"width:300px;\"";
                    if ($isAdmin) {
                        $rstr = "";
                        $bndRankIds = $this->allianceBoardCategoryRankRepository->getRanksForBnd($diplomacy->id);
                        foreach ($rank as $k => $v) {
                            if (in_array($k, $bndRankIds, true)) {
                                $rstr .= $v . ", ";
                            }
                        }

                        if ($rstr != "") $rstr = substr($rstr, 0, strlen($rstr) - 2);
                        echo " " . tm("Admin-Info: " . stripslashes($diplomacy->otherAllianceName),/*"<b>Position:</b> ".$arr['cat_order']."<br/>*/ "<b>Zugriff:</b> " . $rstr) . "";
                    }
                    echo "><b><a href=\"?page=$page&amp;cat=0&bnd=" . $diplomacy->id . "\"";
                    echo ">" . stripslashes($diplomacy->otherAllianceName) . "</a></b><br/>" . BBCodeUtils::toHTML($diplomacy->text) . "</td>";
                    echo "<td>" . $postCounts[$diplomacy->id] . "</td>";
                    echo "<td>" . $topicCounts[$diplomacy->id] . "</td>";
                    echo "<td>$ps</td>";
                    if ($isAdmin) {
                        echo "<td style=\"width:90px;\"><input type=\"button\" value=\"Bearbeiten\" onclick=\"document.location='?page=$page&editbnd=" . $diplomacy->id . "'\" /><br/>
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
            $cid = $this->allianceBoardCategoryRepository->addCategory($form->getData()['catName'], $form->getData()['catDesc'], $form->getData()['catOrder'], $form->getData()['catBullet'], $this->getUser()->getData()->getAllianceId());
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
            $category = $this->allianceBoardCategoryRepository->getCategory($id, $this->getUser()->getData()->getAllianceId());
            if($category) {
                $form = $this->createFormBuilder()
                    ->add('catDelete', SubmitType::class, [
                        'label' => 'Löschen',
                        'attr' => [
                            'onclick' => "return confirm('Willst du die Kategorie " . $category->name . " wirklich löschen?')"
                        ]
                    ])
                    ->getForm();

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $this->allianceBoardCategoryRepository->deleteCategory($id, $this->getUser()->getData()->getAllianceId());
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
                'categoryName' => $category?->name
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

        $category = $this->allianceBoardCategoryRepository->getCategory($id, $this->getUser()->getData()->getAllianceId());

        if($category) {
            $form = $this->buildCategoryForm($category);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->allianceBoardCategoryRepository->updateCategory($id, $category->getCatName(), $category->getCatDesc(), $category->getCatOrder(), $category->getCatBullet(),$this->getUser()->getData()->getAllianceId());
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
        $allianceCategories = $this->allianceBoardCategoryRepository->getCategories($cu->getAllianceId());
        $availableCategories = $this->allianceBoardCategoryRankRepository->getCategoriesForRank($cu->getAllianceId(), $cu->getAllianceRankId());
        if (count($allianceCategories) > 0) {
            foreach ($allianceCategories as $category) {
                $myCat[$category->id] = in_array($category->id, $availableCategories, true);
            }
        }
        $allianceUsers = $this->userRepository->searchUsers(UserSearch::create()->allianceId($cu->getAllianceId()));

        if ($this->isAdmin() || (isset($myCat[$id]) && $myCat[$id])) {
            $category = $this->allianceBoardCategoryRepository->getCategory($id, $cu->getAllianceId());
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
        $category = $this->allianceBoardCategoryRepository->getCategory($id, $this->getUser()->getData()->getAllianceId());

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
            $mid = $this->allianceBoardTopicRepository->addTopic($form->getData()['topicSubject'], 0, $category->id, $cu->getId(), $cu->getNick());
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

        if ($this->getUser()->getId() == $topic->userId || $this->isAdmin()) {
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
                    'choices'=>$this->allianceBoardCategoryRepository->getCategories($this->getUser()->getData()->getAllianceId()),
                    'choice_value' => 'id',
                    'choice_label' => function (?Category $category): string {
                        return $category ? strtoupper($category->name) : '';
                    },
                    'mapped' => false
                ])
                ->add('topicEdit', SubmitType::class, ['label' => 'Speichern'])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->allianceBoardTopicRepository->updateTopic($id, $topic->getTopicSubject(), 0, $form->get('topicCatId')->getData()->id, $topic->top, $topic->closed);
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
            'topicName' => $topic->subject,
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
                'cpost' => $this->allianceBoardPostRepository->getUserAlliancePostCounts($this->getUser()->getData()->getAllianceId(), $this->getUser()->getId()),
                'isAdmin' => $this->isAdmin(),
                'category' => $this->allianceBoardCategoryRepository->getCategory($topic->categoryId , $this->getUser()->getData()->getAllianceId())
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

        if($topic->closed)
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
            return new RedirectResponse($this->generateUrl('game.alliance.allianceboard.showposts',['id'=>$topic->id]));
        }

        return $this->render('game/alliance/allianceboard/allianceboard_newpost.html.twig',[
            'msg' => $msg??null,
            'form' => $form,
            'id' => $topic->categoryId
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

        if($this->getUser()->getId() == $post->userId || $this->isAdmin()) {
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
                    $this->allianceBoardPostRepository->updatePost($id, $post->text);
                else
                    $this->allianceBoardPostRepository->updatePost($id, $post->text, $this->getUser()->getId());
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

        if($this->getUser()->getId() == $post->userId || $this->isAdmin()) {
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
        $userAlliancePermission = $this->service->getUserAlliancePermissions($this->allianceRepository->getAlliance($this->getUser()->getData()->getAllianceId()), $this->getUser()->getData());
        return $userAlliancePermission->hasRights(AllianceRights::ALLIANCE_BOARD);
    }

    private function buildCategoryForm(mixed $data = null):FormInterface
    {
        $alliance = $this->allianceRepository->getAlliance($this->getUser()->getData()->getAllianceId());
        $ranks = $this->allianceRankRepository->getRanks($alliance->id);
        $rank = array();
        foreach ($ranks as $r) {
            $rank[$r->id] = $r->name;
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