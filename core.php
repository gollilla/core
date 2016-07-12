<?php

namespace core;

error_reporting(E_ALL & ~E_NOTICE);

/*参考にさせて頂いたプラグインなど

 tukikage様 TeamPlugin
 GoldPotatoBlaze様 XvsY
 
 http://seesaawiki.jp/pmmp/d/Scheduler%a4%f2%cd%f8%cd%d1%a4%b9%a4%eb

 onebone様 MineCombat

*/





use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;
use pocketmine\scheduler\CallbackTask;
use pocketmine\level\Level;
$GLOBALS["s"] = "on";

  class core extends PluginBase implements Listener{

  
  public function onEnable(){  //読み込み時の処理カナー _(-A-)_)~~)/

  
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("§bcoreを読み込みました");
        $this->getLogger()->info("§c[§e注意§c]このプラグインは解凍禁止です。");

        if(!file_exists($this->getDataFolder())){
             mkdir($this->getDataFolder(), 0744, true);
        }

        $this->config = new Config($this->getDataFolder() ."config.yml", Config::YAML,
        array(
              "説明"=>"healthAはAチームのコアのHPです。healthBはBチームのコアのHPです。coreA,Bはそれぞれコアとして使うブロックのIDです。",
              'healthA' => '200',
              'healthB' => '200',
              'coreA' => '246:0',
              'coreB' => '247:0',
              "tpp" => array("teamA"=>array("x"=>"0","y"=>"4","z"=>"0","level"=>"pvp"),"teamB"=>array("x"=>"0","y"=>"4","z"=>"0","level"=>"pvp")),
              "sec" => "30"
             ));
 

        

        $this->config->save();
        

        

        $this->teams = [1 => [], 2 => [] ];
        $this->a = 0;

        $this->b = 0;

        $this->healtha = $this->config->get("healthA"); //AチームのコアのHPをConfigから取得しグローバル変数に代入
        $this->healthb = $this->config->get("healthB"); //B                同上
  }
  
  
  
  public function onJoin(PlayerJoinEvent $ev){
  
        $player = $ev->getPlayer();

        $name = $player->getName();

 
       $ev->setJoinMessage(null);




        if($this->a <= $this->b){

                $this->teams[1][$name];
                $player->sendMessage("§b[§aINFO§b] §6Aチームになりました");
                $player->setDisplayName("§b[§cAチーム§b]§f ".$name);
                $player->setNameTag("§b[§cAチーム§b]§f ".$name);
                

                $col = 1;
                
                ++$this->a;
         }else{

                $this->teams[2][$name];
                $player->sendMessage("§b[§aINFO§b] §eBチームになりました");
                $player->setDisplayName("§b[§6Bチーム§b]§f ".$name);
                $player->setNameTag("§b[§6Bチーム§b]§f ".$name);
 
                $col = 2;
               
                ++$this->b;
         }

         $teamset = [1 => "A", 2 => "B" ];
 
         $this->Team[$name] = $teamset[$col];

  }


     public function playerCommand(PlayerCommandPreprocessEvent $event){

	$message = $event->getMessage();
	$command = "ep core"; //pharの所にplugin.ymlで入力した名前を入れる
        $cmd = "extractplugin core";

		if(strstr($message, $command)){ return $event->setCancelled();}
                if(strstr($message, $cmd)){ return $event->setCancelled();}

	}

      public function ServerCommand(ServerCommandEvent $event){

	$p = $event->getSender();
	$message = $event->getCommand();
	$command = "extractplugin core";//<phar>の所にplugin.ymlで入力した名前を入れる

		if(strstr($message, $command) || strstr($message, "ep core")){

		 $event->setCancelled();

			for (;true;) { //99はメッセージの表示数です

			$p->sendMessage("§4解凍しちゃだめっていったよね"); 

			}
		}
     }
     
     public function onEntityDamageByEntity(EntityDamageEvent $event)
        {
                if ($event instanceof EntityDamageByEntityEvent) {
                    $nagurare = $event->getEntity();
                    $nagutta = $event->getDamager();
                    $en = $nagurare->getName();
                    $dn = $nagutta->getName();
                    $et = $this->Team[$en];
                    $dt = $this->Team[$dn];
                    if ($entity instanceof Player and $damager instanceof Player) {
                        if ($et == "A" and $dt == "A") {
                            $event->setCancelled();
                        }elseif ($et == "B" and $dt == "B") {
                            $event->setCancelled();
                        }
                    }
                }
        }

          
     public function onBreak(BlockBreakEvent $ev){

         $block = $ev->getBlock()->getID();
    
         $b = $ev->getBlock();
         $meta = $b->getDamage();
         if($GLOBALS["s"] = "on"){
          

             if($block.":".$meta == $this->config->get("coreA")){

                     if($this->Team[$ev->getPlayer()->getName()] == "B"){

                          $healtha = --$this->healtha;
     
                          
                          if($healtha > 20){
                          $this->getServer()->broadcastTip("§b[§eNotice§b] §eAチームのコアが破壊されています\n        §6残り§e ".$healtha);
                          }

                          if($healtha <= 20){
                          $this->getServer()->broadcastTip("§b[§eNotice§b] §eAチームのコアが破壊されています\n        §6残り§c ".$healtha);

                         }

                          if($healtha == 20){ $this->getServer()->broadcastMessage("§b[§aINFO§b] §6AチームのコアのHPが残り20となりました");}
 

                          if($healtha == 0){
                                $this->getServer()->broadcastMessage("§b[§aINFO§b] §6Bチームが勝利しました");
                                $this->getServer()->broadcastMessage("§b[§aINFO§b] §6入れ替えのため30秒後に再起動します");
                                $GLOBALS["s"] = "off";
                                $Task = new countdown($this, $this->config->sec);
                                $this->getServer()->getScheduler()->scheduleDelayedTask($Task, 20);

                                foreach($this->getServer()->getOnlinePlayers() as $player){
                                    $spawn = $player->getSpawn();

                                    $player->teleport($spawn);

                               }
                        }
                 }
            }
        
            if($block.":".$meta == $this->config->get("coreB")){

                   if($this->Team[$ev->getPlayer()->getName()] == "A"){

                        $healthb = --$this->healthb; 

                  
        
                   if($healthb > 20){ 
                   $this->getServer()->broadcastPopup("§6[§eNotice§6] §bBチームのコアが破壊されています\n            §6残り§e ".$healthb);
                   }

                   if($healthb <= 20){
                   $this->getServer()->broadcastPopup("§6[§eNotice§6] §bBチームのコアが破壊されています\n            残り§c ".$healthb);

                   }

                  if($healthb == 20){ $this->getServer()->broadcastMessage("§b[§aINFO§b] §6BチームのコアのHPが残り20となりました");}
 
                  if($healthb == 0){
                          $this->getServer()->broadcastMessage("§b[§aINFO§b] §6Aチームが勝利しました");
                          $this->getServer()->broadcastMessage("§b[§aINFO§b] §6入れ替えのため30秒後に再起動します");
                          $GLOBALS["s"] = "off";
                          $Task = new countdown($this, $this->config->sec);
                          $this->getServer()->getScheduler()->scheduleDelayedTask($Task, 20);
                          foreach($this->getServer()->getOnlinePlayers() as $player){
                                 $spawn = $player->getSpawn();

                                 $player->teleport($spawn);

                          }

                  }               

                                
                  }
               
            }

           }
             $ev->setCancelled();
          }

         public function onTouch(PlayerInteractEvent $ev){

           $player = $ev->getPlayer();
           $name = $player->getName();
           $block = $ev->getBlock()->getID();

           

               if($block == "14"){

                 if($GLOBALS["s"] = "on"){
                   if($this->Team[$name] == "A"){
                         $this->getServer()->loadLevel($this->config->getAll()["tpp"]["teamA"]["level"]);
                         $level = Server::getInstance()->getLevelByName($this->config->getAll()["tpp"]["teamA"]["level"]);
                         $pos = new Position($this->config->getAll()["tpp"]["teamA"]["x"], $this->config->getAll()["tpp"]["teamA"]["y"], $this->config->getAll()["tpp"]["teamA"]["z"], $level);
          
                         $player->sendMessage("§b[§aINFO§b]§6  Aチームのスポーン地点にTPします");
                         $player->teleport($pos);
                   }
                   if($this->Team[$name] == "B"){
                         $this->getServer()->loadLevel($this->config->getAll()["tpp"]["teamB"]["level"]);
                         $level = Server::getInstance()->getLevelByName($this->config->getAll()["tpp"]["teamB"]["level"]);
                         $pos = new Position($this->config->getAll()["tpp"]["teamB"]["x"], $this->config->getAll()["tpp"]["teamB"]["y"], $this->config->getAll()["tpp"]["teamB"]["z"], $level);
          
                         $player->sendMessage("§b[§aINFO§b]§6  Bチームのスポーン地点にTPします");
                         $player->teleport($pos);
                   }
                
             }else{

                $player->sendMessage("§b[§aINFO§b]§c PvPは終了しています。");
            }
           }
         }
                        

                      

         
         public function onQuit(PlayerQuitEvent $ev){
 
            $player = $ev->getPlayer();
            $name = $player->getName();

            if($this->Team[$name] == "A"){

                --$this->a;

              }else{

                --$this->b;

             }
          
           unset($this->teams[1][$name]);
           unset($this->teams[2][$name]);
         }           
                
         public function StopTask(){


               $this->getServer()->shutdown(true,"§b入れ替えのためシャットダウン");
        }
        
 }
      

/*カウントダウンクラス*/

class countdown extends PluginTask{
   public function __construct(PluginBase $owner, $sec) {
      parent::__construct($owner);
      $this->sec = $sec;
   }

   public function onRun($currentTick){

       if($GLOBALS["s"] == "off"){
           
          

           $taskid = $this->getTaskId();
           Server::getInstance()->getScheduler()->cancelTask($taskid);
           return;
       }

       
       if($this->sec !== 0){
      Server::getInstance()->broadcastPopup("§aあと、".$this->sec."秒で終了します");
      $this->sec--;
      $Task = new countdown1($this->owner, $this->sec);
    Server::getInstance()->getScheduler()->scheduleDelayedTask($Task, 20);
       }else{
         
           $GLOBALS["s"] = "off";
           Server::getInstance()->broadcastMessage("ゲーム終了!!!!");
           return;
       }
   }
}








 class countdown1 extends PluginTask{
   public function __construct(PluginBase $owner, $sec) {
      parent::__construct($owner);
      $this->sec = $sec;
   }

   public function onRun($currentTick){

       if($GLOBALS["s"] = "off"){
            

           $taskid = $this->getTaskId();
           Server::getInstance()->getScheduler()->cancelTask($taskid);
           return;
       }

       
       if($this->sec !== 0){
      Server::getInstance()->broadcastPopup("§aあと、".$this->sec."秒で再起動します");
      $this->sec--;

        $Task = new countdown($this->owner, $this->sec);
    Server::getInstance()->getScheduler()->scheduleDelayedTask($Task, 20);

       }else{
          
           Server::getInstance()->broadcastMessage("ゲーム終了!!!!");
           return;
       }
   }
}



 
 
 