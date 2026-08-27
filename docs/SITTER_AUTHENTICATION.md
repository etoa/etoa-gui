# Sitter Authentication

## Überblick

Das System unterstützt jetzt zwei Authentifizierungsmethoden für User-Accounts:

1. **Hauptuser (ROLE_PLAYER)**: Login mit dem normalen Passwort - volle Rechte
2. **Sitter (ROLE_PLAYER_SITTER)**: Login mit dem Gast-Passwort (UserSitting) - eingeschränkte Rechte

## Wie es funktioniert

### 1. Authentifizierung

Der `PlayerAuthenticator` prüft bei jedem Login:
- Gibt es einen aktiven `UserSitting`-Eintrag für den User?
- Stimmt das eingegebene Passwort mit dem Sitter-Passwort überein?
- Falls ja → Login als Sitter mit `ROLE_PLAYER_SITTER`
- Falls nein → Normale Passwort-Prüfung für Hauptuser mit `ROLE_PLAYER`

### 2. CurrentPlayer Erweiterung

Die `CurrentPlayer` Klasse hat folgende neue Methoden:
- `isSitter(): bool` - Prüft ob der aktuelle Login ein Sitter ist
- `getSitting(): ?UserSitting` - Gibt den UserSitting-Eintrag zurück (nur bei Sittern)
- `getRoles(): array` - Gibt `['ROLE_PLAYER_SITTER']` oder `['ROLE_PLAYER']` zurück

## Bereiche für Sitter einschränken

### Mit Security Annotations in Controllern

```php
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserConfigController extends AbstractController
{
    // Nur für Hauptuser, Sitter haben keinen Zugriff
    #[IsGranted('ROLE_PLAYER')]
    #[IsGranted('!ROLE_PLAYER_SITTER')]
    public function editAccount(): Response
    {
        // Account-Einstellungen ändern
    }
    
    // Beide haben Zugriff (Hauptuser erben ROLE_PLAYER)
    #[IsGranted('ROLE_PLAYER')]
    public function viewFleet(): Response
    {
        // Flotte anzeigen
    }
}
```

### Mit Voter für feinere Kontrolle

```php
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SettingsVoter extends Voter
{
    const EDIT = 'edit';
    
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::EDIT;
    }
    
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        
        if (!$user instanceof CurrentPlayer) {
            return false;
        }
        
        // Sitter dürfen keine Einstellungen ändern
        if ($user->isSitter()) {
            return false;
        }
        
        return true;
    }
}
```

### In Twig Templates

```twig
{# Nur für Hauptuser anzeigen #}
{% if is_granted('ROLE_PLAYER') and not is_granted('ROLE_PLAYER_SITTER') %}
    <a href="{{ path('settings_account') }}">Account-Einstellungen</a>
{% endif %}

{# Sitter-Status anzeigen #}
{% if is_granted('ROLE_PLAYER_SITTER') %}
    <div class="alert alert-info">
        Du bist als Gast eingeloggt. Einige Funktionen sind eingeschränkt.
    </div>
{% endif %}

{# Im Controller-PHP #}
{{ app.user.isSitter ? 'Gast-Modus' : 'Hauptuser' }}
```

### Programmatisch im Controller

```php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SomeController extends AbstractController
{
    public function someAction(Security $security): Response
    {
        /** @var CurrentPlayer $currentPlayer */
        $currentPlayer = $security->getUser();
        
        if ($currentPlayer->isSitter()) {
            // Eingeschränkte Funktionalität für Sitter
            $this->addFlash('warning', 'Als Gast hast du eingeschränkte Rechte.');
        }
        
        // Alternative mit Security-Service
        if ($security->isGranted('ROLE_PLAYER_SITTER')) {
            // Sitter-spezifische Logik
        }
    }
}
```

## Typische Einschränkungen für Sitter

Empfohlene Bereiche, die für Sitter gesperrt werden sollten:

1. **Account-Verwaltung**
   - Passwort ändern
   - E-Mail ändern
   - Account löschen
   - 2FA-Einstellungen

2. **Sensible Aktionen**
   - Allianz verlassen
   - Handel mit Premium-Währung
   - Account-Übertragung

3. **Konfigurationen**
   - Design/Theme ändern
   - Sprache ändern (optional)

Bereiche, die Sitter normalerweise nutzen dürfen:

1. **Spielmechanik**
   - Flotten bewegen
   - Gebäude bauen
   - Forschung betreiben
   - Nachrichten lesen/schreiben

2. **Ansichten**
   - Statistiken
   - Galaxie-Karte
   - Berichte

## Beispiel: UserConfig Controller absichern

```php
namespace EtoA\Controller\Game;

use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserConfigController extends AbstractController
{
    #[IsGranted('ROLE_PLAYER')]
    public function index(): Response
    {
        // Jeder eingeloggte Spieler kann die Übersicht sehen
    }
    
    #[IsGranted('ROLE_PLAYER')]
    #[IsGranted('!ROLE_PLAYER_SITTER')]
    public function changePassword(): Response
    {
        // Nur Hauptuser dürfen das Passwort ändern
    }
    
    #[IsGranted('ROLE_PLAYER')]
    #[IsGranted('!ROLE_PLAYER_SITTER')]
    public function changeEmail(): Response
    {
        // Nur Hauptuser dürfen die E-Mail ändern
    }
}
```

## Security-Konfiguration

Die Role-Hierarchie in `config/packages/security.yaml`:

```yaml
role_hierarchy:
    ROLE_PLAYER_SITTER: ROLE_PLAYER
```

Dies bedeutet: Sitter erben die Basis-Rolle `ROLE_PLAYER`, können aber mit `!ROLE_PLAYER_SITTER` ausgeschlossen werden.

## Datenbank

Die UserSitting-Tabelle enthält:
- `user_id`: Der Hauptuser-Account
- `sitter_id`: Der User, der als Sitter fungiert (oft NULL bei externen Sittern)
- `password`: Das gehashte Gast-Passwort
- `dateFrom`: Start-Zeitpunkt des Sitter-Zugangs
- `dateTo`: End-Zeitpunkt des Sitter-Zugangs

Der Authenticator prüft automatisch, ob ein aktiver Sitter-Eintrag existiert (dateFrom < now < dateTo).
