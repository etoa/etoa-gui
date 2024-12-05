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
use EtoA\Entity\AllianceBoardPost;
use EtoA\Entity\AllianceBoardTopic;
use EtoA\Entity\AllianceDiplomacy;
use EtoA\Form\Type\Core\AllianceBoardPostType;
use EtoA\Image\ImageUtil;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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

            echo '<table class="tb">';
            echo "<tr><th colspan=\"2\">Kategorie</th><th>Posts</th><th>Topics</th><th>Letzter Beitrag</th>";
            if ($isAdmin) {
                echo "<th style=\"width:50px;\">Aktionen</th>";
            }
            echo "</tr>";
            $accessCnt = 0;
            foreach ($categories as $category) {

                $postCounts = $this->allianceBoardTopicRepository->getTopicPostCountsByCategory($categoryIds);

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
                    echo "<td>" . count($category->getTopics()) . "</td>";
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
                    $post = $this->allianceBoardTopicRepository->getTopicWithLatestPost(0, $diplomacy->getId());
                    if ($post !== null) {
                        $ps = "<a href=\"?page=$page&amp;topic=" . $topic->getId() . "#" . $post->getId() . "\" " . tm($post->getTopic()->getSubject() . ", " . StringUtils::formatDate($topic->getTimestamp()), "Geschrieben von: <b>" . $post->getUserNick() . "</b>") . ">" . $topic->getSubject() . "<br/>" . StringUtils::formatDate($topic->getTimestamp()) . "</a>"; //ToDo User auch von anderen Allianzen
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
    //TODO: use cascade
    #[Route('/game/allianceboard/deletecategory/{id}', name: 'game.alliance.allianceboard.deletecategory')]
    public function deleteCategory(Request $request, ?AllianceBoardCategory $category = null): Response {
        if($this->isAdmin()) {
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
                    $this->allianceBoardCategoryRepository->deleteCategory($category->getId(), $this->getUser()->getData()->getAlliance()->getId());
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
    #[Route('/game/allianceboard/editcategory/{id}', name: 'game.alliance.allianceboard.editcategory')]
    public function editCategory(Request $request, ?AllianceBoardCategory $category = null): Response {
        if(!$this->isAdmin()) {
            return $this->redirectToRoute('game.alliance.allianceboard.overview');
        }

        if($category) {
            $form = $this->buildCategoryForm($category);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->allianceBoardCategoryRepository->save();
                $newRanks = array_map(fn ($value) => (int) $value, $form->get('rank')->getData() ?? []);
                $this->allianceBoardCategoryRankRepository->replaceRanks($category->getId(), 0, $newRanks);
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
    #[Route('/game/allianceboard/showtopics/{id}',
        name: 'game.alliance.allianceboard.showtopics'
    )]
    public function showTopics(?AllianceBoardCategory $category = null): Response {
        $myCat = [];
        $cu = $this->getUser()->getData();
        $allianceCategories = $this->allianceBoardCategoryRepository->findBy(['alliance'=>$cu->getAlliance()->getId()],['order'=>'DESC','name'=>'DESC']);
        $availableCategories = $this->allianceBoardCategoryRankRepository->getCategoriesForRank($cu->getAlliance()->getId(), $cu->getAllianceRankId());
        if (count($allianceCategories) > 0) {
            foreach ($allianceCategories as $allianceCategory) {
                $myCat[$allianceCategory->getId()] = in_array($allianceCategory->getId(), $availableCategories, true);
            }
        }

        if ($this->isAdmin() || (isset($myCat[$category->getId()]) && $myCat[$category->getId()])) {
            if (!$category || $category->getAlliance() !== $this->getUser()->getData()->getAlliance()) {
                return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                    'message' => 'Kategorie existiert nicht!'
                ]);
            }
        } else
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Kein Zugriff!'
            ]);

        return $this->render('game/alliance/allianceboard/allianceboard_topics.html.twig',[
            'category' => $category,
            'isAdmin' => $this->isAdmin(),
            'topics' => $this->allianceBoardTopicRepository->findBy(['category'=>$category->getId()]),
            'allianceBoardPostRepository' => $this->allianceBoardPostRepository
        ]);
    }

    //create new topic
    #[Route('/game/allianceboard/newtopic/{id}', name: 'game.alliance.allianceboard.newtopic')]
    public function newTopic(Request $request, ?AllianceBoardCategory $category = null): Response {
        if(!$category)
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Diese Kategorie existiert nicht!'
            ]);

        $topic = new AllianceBoardTopic();

        $form = $this->createFormBuilder($topic)
            ->add('subject', TextType::class,
                [
                    'attr' => ['size'=>40],
                    'constraints'=> new NotBlank([
                        'message' => 'Du musst einen Text eingeben!',
                    ]),
                ]
            )
            ->add('posts', CollectionType::class, array(
                'entry_type' => AllianceBoardPostType::class,
                'allow_add' => true,
                'label' => false
            ))
            ->add('submit', SubmitType::class, ['label' => 'Speichern'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cu = $this->getUser()->getData();

            $topic->setBndId(0);
            $topic->setCategory($category);
            $topic->setUser($cu);
            $topic->setUserNick($cu->getNick());
            $topic->setTimestamp(time());

            $topic->getPosts()->getValues()[0]->setTopic($topic);
            $topic->getPosts()->getValues()[0]->setUserId($cu->getId());
            $topic->getPosts()->getValues()[0]->setUserNick($cu->getNick());
            $topic->getPosts()->getValues()[0]->setTimestamp(time());

            $this->allianceBoardTopicRepository->persist($topic);
            $this->allianceBoardPostRepository->save();

            return new RedirectResponse($this->generateUrl('game.alliance.allianceboard.showtopics',['id'=>$category->getId()]));
        }

        return $this->render('game/alliance/allianceboard/allianceboard_newtopic.html.twig',[
            'category' => $category,
            'form' => $form
        ]);
    }

    //edit topic
    #[Route('/game/allianceboard/edittopic/{id}', name: 'game.alliance.allianceboard.edittopic')]
    public function editTopic(Request $request, ?AllianceBoardTopic $topic = null): Response {
        if (!$topic) {
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Datensatz nicht gefunden!'
            ]);
        }

        if ($this->getUser() === $topic->getUser() || $this->isAdmin()) {
            $form = $this->createFormBuilder($topic)
                ->add('subject', TextType::class,
                    [
                        'attr' => ['size'=>40],
                        'constraints'=> new NotBlank([
                            'message' => 'Du musst einen Text eingeben!',
                        ]),
                    ]
                )
                ->add('top', CheckboxType::class,[
                    'required' => false
                ])
                ->add('closed', CheckboxType::class,[
                    'required' => false
                ])
                ->add('category', ChoiceType::class, [
                    'choices'=>$this->allianceBoardCategoryRepository->findBy(['alliance'=>$this->getUser()->getData()->getAlliance()->getId()]),
                    'choice_value' => 'id',
                    'choice_label' => function (?AllianceBoardCategory $category): string {
                        return $category ? strtoupper($category->getName()) : '';
                    }
                ])
                ->add('topicEdit', SubmitType::class, ['label' => 'Speichern'])
                ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->allianceBoardTopicRepository->save();
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
    public function deleteTopic(Request $request, ?AllianceBoardTopic $topic = null): Response
    {
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
            $this->allianceBoardTopicRepository->remove($topic);
            $this->allianceBoardTopicRepository->save();

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
    public function showPosts(AllianceBoardTopic $topic): Response
    {
        if($topic->getPosts()) {
            return $this->render('game/alliance/allianceboard/allianceboard_posts.html.twig',[
                'userRepository' => $this->userRepository,
                'topic' => $topic,
                'cpost' => $this->allianceBoardPostRepository->getUserAlliancePostCounts($this->getUser()->getData()->getAlliance()->getId(), $this->getUser()->getId()),
                'isAdmin' => $this->isAdmin()
            ]);
        } else {
            if ($topic) {
                $this->allianceBoardTopicRepository->remove($topic);
                $this->allianceBoardTopicRepository->save();
            }
            return new RedirectResponse($this->generateUrl('game.alliance.allianceboard.overview'));
        }
    }

    //create new post
    #[Route('/game/allianceboard/newpost/{id}', name: 'game.alliance.allianceboard.newpost')]
    public function newPost(Request $request, ?AllianceBoardTopic $topic = null): Response
    {
        if(!$topic)
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Dieses Thema existiert nicht!'
            ]);

        if($topic->isClosed())
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Dieses Thema ist geschlossen!'
            ]);


        $post = new AllianceBoardPost();
        $form = $this->createFormBuilder($post)
            ->add('submit', SubmitType::class, ['label' => 'Beitrag speichern'])
            ->add('text', TextareaType::class,
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
            $post->setTopic($topic);
            $post->setUserId($this->getUser()->getId());
            $post->setUserNick($this->getUser()->getUserIdentifier());
            $post->setTimestamp(time());
            $this->allianceBoardPostRepository->persist($post);

            $topic->setTimestamp(time());

            $this->allianceBoardPostRepository->save();

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
    public function editPost(Request $request, ?AllianceBoardPost $post = null): Response
    {
        $allowed = $this->isAdmin() || $post->getUserId() === $this->getUser()->getId();

        if(!$post || !$allowed)
            return $this->render('game/alliance/allianceboard/allianceboard_notfound.html.twig',[
                'message' => 'Datensatz nicht gefunden!'
            ]);

        if($this->getUser()->getId() == $post->getUserId() || $this->isAdmin()) {
            $form = $this->createFormBuilder($post)
                ->add('postEdit', SubmitType::class, ['label' => 'Speichern'])
                ->add('text', TextareaType::class,
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
                $post->setChanged(time());
                $this->allianceBoardPostRepository->save();
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
    public function deletePost(Request $request, ?AllianceBoardPost $post = null): Response
    {
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
                $this->allianceBoardPostRepository->remove($post);
                $this->allianceBoardPostRepository->save();
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
        $alliance = $this->getUser()->getData()->getAlliance();
        $ranks = $this->allianceRankRepository->findBy(['alliance'=>$alliance->getId()],['level'=>'DESC']);
        $rank = array();
        foreach ($ranks as $r) {
            $rank[$r->getId()] = $r->getName();
        }

        $boardBulletDir = 'build/images/boardbullets';

        $d = opendir($boardBulletDir);
        $bullets = array();
        $bullets[AllianceBoardAvatar::DEFAULT_IMAGE] = 'Standard-Symbol';

        while ($f = readdir($d)) {
            if (is_file($boardBulletDir . "/" . $f) && !is_dir($boardBulletDir . "/" . $f) && $f != AllianceBoardAvatar::DEFAULT_IMAGE) {
                $bullets[$f] = $f;
            }
        }

        return $this->createFormBuilder($data)
            ->add('name', TextType::class,
                [
                    'attr' => ['size'=>40],
                    'constraints'=> new NotBlank([
                        'message' => 'Du musst einen Text eingeben!',
                    ]),
                ]
            )
            ->add('description', TextType::class, [
                'attr' => ['size'=>40],
                'required' => false
            ])
            ->add('order', NumberType::class, [
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
            ->add('bullet', ChoiceType::class, [
                'choices'=>array_flip($bullets),
                'placeholder' => false,
                'attr'=>['data-model'=>"on(change)|image"],
                'required' => false
            ])
            ->add('catSave', SubmitType::class, ['label' => 'Kategorie speichern'])
            ->getForm();
    }
}