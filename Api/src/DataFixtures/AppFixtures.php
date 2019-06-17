<?php

namespace App\DataFixtures;

use App\Controller\AuthController;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
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
		self::_addUsers($populator, $generator);
		self::_addCategories($populator, $generator);
		self::_addArticles($populator, $generator);
		$populator->execute();
	}

	private static function _addArticles(Populator $populator, Generator $generator): void
	{
		$populator->addEntity(
			Article::class,
			50,
			[
				'images' => self::_getImagesFormatter($generator),
				'title' => self::_getNameFormatter($generator),
			]
		);
	}

	private static function _addCategories(Populator $populator, Generator $generator): void
	{
		$populator->addEntity(
			Category::class,
			10,
			['name' => self::_getNameFormatter($generator)]
		);
	}

	private static function _addUsers(Populator $populator, Generator $generator): void
	{
		$populator->addEntity(
			User::class,
			5,
			[
				'roles' => self::_getRolesFormatter(),
				'token' => null,
				'token_expiration' => null,
			],
			[self::getUserTokenModifier()]
		);
	}

	private static function _getNameFormatter(Generator $generator): \Closure
	{
		return static function () use ($generator): string {
			return $generator->name;
		};
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
