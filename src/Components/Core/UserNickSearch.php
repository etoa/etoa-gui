<?php

namespace EtoA\Components\Core;

use EtoA\Controller\Game\AbstractGameController;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/user_nick_search.html.twig')]
class UserNickSearch extends AbstractGameController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $field;

    #[LiveProp(writable: true)]
    public string $value = '';

    #[LiveProp(writable: true)]
    public string $boxId;

    public function __construct(
        private readonly UserRepository $userRepository
    )
    {}

    public function getUsers(): string
    {
        if($this->value) {
            $nicknames = $this->userRepository->searchUserNicknames(UserSearch::create()->nickLike($this->value), 20);
            $sOut = '<div id="userbox">';

            foreach ($nicknames as $nickname) {
                $sOut .= "<div><a href=\"#\" onclick=\"document.getElementById('" . $this->boxId . "').value=(document.getElementById('" . $this->boxId . "').value && document.getElementById('" . $this->boxId . "').value.indexOf(';')!=-1)?document.getElementById('" . $this->boxId . "').value.replace(/^(.+);[^;]+$/,'$1;')+'" . htmlentities($nickname, ENT_QUOTES, 'UTF-8') . "':'" . htmlentities($nickname, ENT_QUOTES, 'UTF-8') . "';document.getElementById('userbox').style.display = 'none';\">" . htmlentities($nickname, ENT_QUOTES, 'UTF-8') . "</a></div>";
            }

            $sOut .= '</div>';

            return $sOut;
        }
        return '';
    }
}