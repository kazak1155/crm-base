<?php
//require_once $_SERVER['DOCUMENT_ROOT']."/templates/calls/prime/signal.php";
?>
<div class="menu-cont">
	<ul class="menu-main-ul">
		<? if($this->User->user_group_name === 'adm'
            || (
                    $this->User->user_group_name === 'tig_logistics'
                    || $this->User->user_group_name === 'tig_wh'
                    || $this->User->user_group_name === 'via_cross_tig'
                    || $this->User->user_group_name === 'arredo_cross_tig'
            )): ?>
		<li>
			<a>Логистика</a>
			<ul>
				<li>
					<a>Логистика</a>
					<ul>
						<li><a href="tig/orders">Заказы</a></li>
						<li><a href="tig/hauls">Рейсы</a></li>
						<li><a href="tig/route">Маршрут</a></li>
						<li><a href="tig/monitoring">Мониторинг</a></li>
						<li><a href="tig/hauls_details">Состав рейсов</a></li>
						<? if(($this->User->user_group_name === 'adm')||($this->User->user_group_name === 'tig_logistics')): ?>
							<li>
								<a href="tig/statistics">
									Статистика
									<span class="ddmenu-icon"><i class="fa fa-area-chart"></i></span>
								</a>
							</li>

							<li><a href="tig/journal">Журнал</a></li>
							<li><a href="tig/cargos">Грузы</a></li>
							<li><a href="tig/reference">Справочники</a></li>
						<? endif; ?>
					</ul>
				</li>
				<? if($this->User->user_group_name === 'adm'): ?>
				<li>
					<a>Услуги</a>
					<ul>
						<li><a href="services/delivery_ru">Доставка до РФ</a></li>
						<li><a href="services/service_rates">Тарификация услуг</a></li>
						<li><a href="services/rates_moscow_adm">Тарификация РФ адм.</a></li>
					</ul>
				</li>
				<li>
					<a>Разрешит. документы</a>
					<ul>
						<li><a href="tig/customs_docs">Документы</a></li>
						<li><a href="tig/customs_docs_ext">Поиск</a></li>
					</ul>
				</li>				
				<? endif; ?>
			</ul>
		</li>
		<? endif; ?>

		<!--
		<?php if($this->User->user_group_name === 'adm' || $this->User->user_group_name === 'external_users'):?>
		<li>
			<a>External</a>
			<ul>
				<? if(strtolower($this->User->user_login) === 'depo1'): ?>
					<li><a href="external/depo_orders">Depo</a></li>
				<? endif; ?>
				<? if(strtolower($this->User->user_login) === 'formaro1'): ?>
					<li><a href="external/formaro_orders">Formaro</a></li>
				<? endif; ?>
				<? if(strtolower($this->User->user_login) === 'mgsystem1'): ?>
					<li><a href="external/mgsystem_orders">MG System</a></li>
				<? endif; ?>
			</ul>
		</li>
		<?php endif; ?>
		-->
		<?php if($this->User->user_group_name !== 'new' && (strpos('b4p', (string)$this->User->user_group_name) === false) && (strpos('external_users', (string)$this->User->user_group_name) === false)):?>
		<li>
			<a>Контрагенты</a>
			<ul>
				<li>
					<a href="agents/info">
						Контакты
						<span class="ddmenu-icon"><i class="fa fa-address-card"></i></span>
					</a>
				</li>
				<li>
					<a href="agents/statistics">
						Статистика
						<span class="ddmenu-icon"><i class="fa fa-area-chart"></i></span>
					</a>
				</li>
				<li>
					<a href="agents/clients">
						Клиенты
						<span class="ddmenu-icon"><i class="fa fa-area-chart"></i></span>
					</a>
				</li>
				<?php if($_SESSION['usr']['rgt']['bask'][19] == 1){ ?>
				<li>
					<a href="/templates/calls/prime/index.php">
						Обращения
						<span class="ddmenu-icon"><i class="fa fa-phone"></i></span>
					</a>
				</li>
				<?php } ?>
			</ul>
		</li>
		<?php endif; ?>
		<?php if($this->User->user_group_name === 'adm1'):?>
		<li>
			<a><?php echo $this->lang('adm') ?></a>
			<ul>
				<li><a href="adm/users">Список пользователей</a></li>
				<?php if($_SESSION['usr']['rgt']['bask'][3] == 1){ ?>
				<li><a href="/templates/calls/uspref/index.php">Настройки пользователей</a></li>
				<li><a href="/templates/bill/grp/index.php">Группы</a></li>
				<?php } ?>
				<?php if($_SESSION['usr']['rgt']['bask'][5] == 1){ ?>
				<li><a>Права групп</a>
					<ul>
						<li><a href="/templates/calls/rgtot/index.php">Общие</a></li>
						<li><a href="/templates/bill/rgt/index.php">Счета</a></li>
						<li><a href="/templates/calls/rgt/index.php">Обращения</a></li>
					</ul>
				</li>
				<li><a href="/templates/bill/hol/index.php">
						Праздники
						<span class="ddmenu-icon"><i class="fa fa-calendar"></i></span>
					</a>

				</li>
				<li><a href="/templates/crm/modalwin.php">
							Шаблоны
							<span class="ddmenu-icon"><i class="fa fa-briefcase"></i></span>
					</a>
				</li>



				<?php } ?>
				<?php if($_SESSION['usr']['rgt']['bask'][21] == 1){ ?>
					<li><a>Обращения</a>
						<ul>
							<li><a href="/templates/calls/themes/index.php">Темы</a></li>
							<li><a href="/templates/calls/titles/index.php">Типы обращений</a></li>
						</ul>
					</li>
				<?php } ?>
				<li><a href="adm/users_permissions">Права доступа</a></li>
				<li><a href="adm/tlc_visits">Посещаемость tlc-online</a></li>
				<li><a href="adm/online_users">Список соединений</a></li>
				<li><a href="adm/reference">Системные библиотеки</a></li>
			</ul>
		</li>
		<?php endif; ?>
		<!--
		<li>
			<a href="agents/info" target="_blank">Контакты<span class="ddmenu-icon"><i class="fa fa-address-card"></i></span>
			</a>
		</li>
		-->
		<li class="direct-actions user"><a><?php echo /*$this->lang('log_as').': '.*/'<strong style="font-style:regular">'.$this->User->user_name; ?></strong></a></li>
		<li class="direct-actions" title="<?php echo $this->lang('quit') ?>"><a id="logout"><i class="fa fa-lg fa-sign-out"></i></a></li>
		<li class="direct-actions" title="<?php echo $this->lang('refresh') ?>"><a id="killss" ><i class="fa fa-lg fa-retweet"></i></a></li>
		<li class="direct-actions" title="Очистить кэш"><a id="eraseCache"><i class="fa fa-lg fa-eraser"></i></a></li>
		<!--<li class="direct-actions" title="Задачи"><a id="mission"><i class="fa fa-lg " id="nMessa"></i></a></li>-->
	</ul>
</div>
