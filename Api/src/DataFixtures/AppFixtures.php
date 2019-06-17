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

class AppFixtures extends Fixture
{


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
				'password' => self::_getPasswordFormatter(),
				'roles' => self::_getRolesFormatter(),
				'token' => null,
				'token_expiration' => self::_getTokenExpirationFormatter($generator),
			],
			[self::getUserModifier()]
		);
	}

	private static function getUserModifier(): \Closure
	{
		return static function (User $user) {
			$tokenData = AuthController::tokenGenerator(2, $user->getEmail());
			$tokenExpiration = new \DateTime(date('Y-m-d H:i:s', $tokenData['expire']));
			$user->setToken($tokenData['token'])
				->setTokenExpiration($tokenExpiration);
		};
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

	private static function _getPasswordFormatter(): \Closure
	{
		return static function () {
			return \password_hash('qwerty', PASSWORD_ARGON2I);
		};
	}

	private static function _getImagesFormatter(Generator $generator): \Closure
	{
		return static function () use ($generator) {
			$images = [];
			for ($i = $generator->numberBetween(0, 3); $i > 0; --$i) {
				$images[] = new File($generator->file('../fixture_images', 'public/uploads/images'));
			}

			return $images;
		};
	}

	/**
	 * @param Generator $generator
	 * @return \Closure
	 */
	private static function _getTokenExpirationFormatter(Generator $generator): \Closure
	{
		return static function () use ($generator) {
			return $generator->dateTimeBetween('now', '+30 years');
		};
	}


}
