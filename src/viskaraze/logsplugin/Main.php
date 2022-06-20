<?php

declare(strict_types=1);

namespace viskaraze\logsplugin;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use pocketmine\player\Player;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

    public function onEnable(): void
    {
        $this->saveResource("config.yml");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $web = new Webhook($this->getConfig()->get("webhookConsole"));
        $mes = new Message();
        $emb = new Embed();
        $emb->setColor(0x4DDB00);
        $emb->setTitle($this->getConfig()->get("title-embed-Console"));
        $emb->setDescription("**Serveur start !**");
        $mes->addEmbed($emb);
        $web->send($mes);

    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $name = $player->getName();
        $web = new Webhook($this->getConfig()->get("webhookJoin"));
        $mes = new Message();
        $emb = new Embed();
        $emb->setColor(0x4DDB00);
        $emb->setTitle($this->getConfig()->get("title-embed-Join"));
        $emb->setDescription("**$name** a rejoint le serveur !");
        $mes->addEmbed($emb);
        $web->send($mes);
    }

    public function onQuit(PlayerQuitEvent $event){
        $name = $event->getPlayer()->getName();
        $web = new Webhook($this->getConfig()->get("webhookQuit"));
        $mes = new Message();
        $emb = new Embed();
        $emb->setColor(0xFF0000);
        $emb->setTitle($this->getConfig()->get("title-embed-Quit"));
        $emb->setDescription("**$name** a quitté le serveur ! ");
        $mes->addEmbed($emb);
        $web->send($mes);

    }

    public function onChat(PlayerChatEvent $event){

        $message = $event->getMessage();
        $player = $event->getPlayer();
        $name = $player->getName();
        $web = new Webhook($this->getConfig()->get("webhookChat"));
        $mes = new Message();
        $emb = new Embed();
        $emb->setColor(0x4DDB00);
        $emb->setTitle($this->getConfig()->get("title-embed-Chat"));
        $emb->setDescription("**$name** » $message");
        $mes->addEmbed($emb);
        $web->send($mes);
    }

    public function onPlayerDeath(PlayerDeathEvent $event) {

        $victim = $event->getPlayer();
        $victime = $victim->getName();
        $cause = $victim->getLastDamageCause();
        if($cause instanceof EntityDamageByEntityEvent){
            $damager = $cause->getDamager();
            if($damager instanceof Player){
                $killer = $victim->getLastDamageCause()->getDamager()->getName();

                $web = new Webhook($this->getConfig()->get("webhookKills"));
                $mes = new Message();
                $emb = new Embed();
                $emb->setColor(0x4DDB00);
                $emb->setTitle($this->getConfig()->get("title-embed-Kills"));
                $emb->setDescription("**Pseudo :** $killer \n**Victime :** $victime");
                $mes->addEmbed($emb);
                $web->send($mes);

                $web1 = new Webhook($this->getConfig()->get("webhookDeaths"));
                $mes1 = new Message();
                $emb1 = new Embed();
                $emb1->setColor(0x4DDB00);
                $emb1->setTitle($this->getConfig()->get("title-embed-Deaths"));
                $emb1->setDescription("**Pseudo :** $victime \n**Killeur :** $killer");
                $mes1->addEmbed($emb1);
                $web1->send($mes1);

            }else{

                $web2 = new Webhook($this->getConfig()->get("webhookDeaths"));
                $mes2 = new Message();
                $emb2 = new Embed();
                $emb2->setColor(0x4DDB00);
                $emb2->setTitle($this->getConfig()->get("title-embed-Deaths"));
                $emb2->setDescription("**Pseudo :** $victime \n**Killeur :** __entité__");
                $mes2->addEmbed($emb2);
                $web2->send($mes2);

            }
        }else{
            $web3 = new Webhook($this->getConfig()->get("webhookDeaths"));
            $mes3 = new Message();
            $emb3 = new Embed();
            $emb3->setColor(0x4DDB00);
            $emb3->setTitle($this->getConfig()->get("title-embed-Deaths"));
            $emb3->setDescription("**Pseudo :** $victime \n**Killeur :** __Autre Motif__");
            $mes3->addEmbed($emb3);
            $web3->send($mes3);

        }
    }

    public function onPlayerCmd(PlayerCommandPreprocessEvent $event){
        $sender = $event->getPlayer();
        $pseudo = $sender->getName();
        $msg = $event->getMessage();
        $words = explode(" ", $msg);

        if ($msg[0] == "/") {
            $web = new Webhook($this->getConfig()->get("webhookCommands"));
            $mes = new Message();
            $emb = new Embed();
            $emb->setColor(0x4DDB00);
            $emb->setTitle($this->getConfig()->get("title-embed-Commands"));
            $emb->setDescription("**Joueur :** $pseudo \n**Commande : ** /$msg");
            $mes->addEmbed($emb);
            $web->send($mes);
        }
    }
    public function onDisable(): void
    {
        $web = new Webhook($this->getConfig()->get("webhookConsole"));
        $mes = new Message();
        $emb = new Embed();
        $emb->setColor(0x4DDB00);
        $emb->setTitle($this->getConfig()->get("title-embed-Console"));
        $emb->setDescription("**Serveur Closed**");
        $mes->addEmbed($emb);
        $web->send($mes);
    }
}