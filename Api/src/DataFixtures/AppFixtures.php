<?php

namespace App\DataFixtures;

use App\Controller\AuthController;
use App\Entity\Address;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\StockOrder;
use App\Entity\StockOrderItem;
use App\Entity\User;
use App\Entity\UserOrder;
use App\Entity\UserOrderItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Faker\ORM\Doctrine\Populator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $userPasswordEncoder;


	public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
	{
		$this->userPasswordEncoder = $userPasswordEncoder;
	}

	public function load(ObjectManager $manager): void
	{
		$generator = Factory::create();
		$populator = new Populator($generator, $manager);
		$this->_addUsers($populator);
		$populator->addEntity(Address::class, 5);
		static::_addCategories($populator, $generator);
		static::_addArticles($populator, $generator);
		$populator->addEntity(UserOrder::class, 10);
		$populator->addEntity(UserOrderItem::class, 50);
		$populator->addEntity(StockOrder::class, 10);
		$populator->addEntity(StockOrderItem::class, 50);
		$populator->execute();
	}

	private static function _addArticles(Populator $populator, Generator $generator): void
	{
		$populator->addEntity(
			Article::class,
			50,
			[
				'images' => static::_getImagesFormatter($generator),
				'title' => [$generator, 'name'],
			]
		);
	}

	private static function _addCategories(Populator $populator, Generator $generator): void
	{
		$populator->addEntity(
			Category::class,
			10,
			['name' => [$generator, 'name']]
		);
	}

	private function _addUsers(Populator $populator): void
	{
		$populator->addEntity(
			User::class,
			5,
			[
				'roles' => static::_getRolesFormatter(),
				'token' => null,
				'token_expiration' => null,
			],
			[static::getUserTokenModifier(), $this->_getUserPasswordModifier()]
		);
	}

	private static function _getRolesFormatter(): \Closure
	{
		return static function () {
			return ['ROLE_USER', 'ROLE_ADMIN'];
		};
	}

	private static function _getImagesFormatter(Generator $generator): \Closure
	{
		return static function () use ($generator) {
			$images = [];
			for ($i = $generator->numberBetween(0, 3); $i > 0; --$i) {
				$images[] = new File(
					$generator->file(
						'../fixture_images',
						'public/uploads/images'
					)
				);
			}

			return $images;
		};
	}

	private static function getUserTokenModifier(): \Closure
	{
		return static function (User $user) {
			$tokenData = AuthController::tokenGenerator(2, $user->getEmail());
			$tokenExpiration = new \DateTime(
				date('Y-m-d H:i:s', $tokenData['expire'])
			);
			$user->setToken($tokenData['token'])
				->setTokenExpiration($tokenExpiration);
		};
	}

	private function _getUserPasswordModifier(): \Closure
	{
		return function (User $user) {
			$password = $this->userPasswordEncoder
				->encodePassword($user, 'qwerty');
			$user->setPassword($password);
		};
	}


}
