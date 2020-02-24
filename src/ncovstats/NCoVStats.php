<?php

declare(strict_types=1);

namespace NCoVStats;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class NCoVStats extends PluginBase {

	public function onEnable() : void {
		require_once('VirusUtils.php');
		$this->getLogger()->info('Enabled nCoVStats.');
	}

	public function onDisable() : void {
		$this->getLogger()->info('Disabled nCoVStats.');
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
		$this->getScheduler()->scheduleTask(new class($sender, $args) extends Task {
			private $sender, $args;

			public function __construct(CommandSender $sender, array $args) {
				$this->sender = $sender;
				$this->args = $args;
			}

			public function onRun(int $currentTick) : void{
				if(count($this->args) < 1) {
					$this->sender->sendMessage('§6正在获取数据中...');
					$overall = virus_overall();
					if($overall) {
						$this->sender->sendMessage(implode("\n", array(
							'§b§l目前, 全国新冠肺炎统计信息如下:',
							'§c全国确诊: '.$overall['confirmedCount'],
							'§e疑似病例: '.$overall['suspectedCount'],
							'§a治愈人数: '.$overall['curedCount'],
							'§7死亡人数: '.$overall['deadCount']
						)));
					}
					else {
						$this->sender->sendMessage('§c无法从接口获取信息.');
					}
				}
				else {
					$this->sender->sendMessage('§6正在获取数据中...');
					$area = virus_province($this->args[0]);
					if($area) {
						$this->sender->sendMessage(implode("\n", array(
							'§b§l目前, '.$area['provinceName'].'新冠肺炎统计信息如下:',
							'§c全国确诊: '.$area['confirmedCount'],
							'§e疑似病例: '.$area['suspectedCount'],
							'§a治愈人数: '.$area['curedCount'],
							'§7死亡人数: '.$area['deadCount']
						)));
					}
					else {
						$this->sender->sendMessage('§c输入的区域名无效.');
					}
				}
			}
		});
		return true;
	}
}
