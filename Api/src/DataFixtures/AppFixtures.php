<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Faker\ORM\Doctrine\Populator;

class AppFixtures extends Fixture
{

	public function load(ObjectManager $manager)
	{
		$generator = Factory::create();
		$populator = new Populator($generator, $manager);

		$populator->addEntity(
			User::class,
			5,
			[
				'password' => self::_getPasswordClosure($generator),
				'roles' => self::_getRolesClosure(),
				'token' => self::_getTokenClosure($generator),
				'token_expiration' => self::_getTokenExpirationClosure($generator),
			]
		);
		$populator->addEntity(
			Category::class,
			10,
			['name' => self::_getNameClosure($generator)]
		);
		$populator->addEntity(
			Article::class,
			50,
			[
//				'images' => self::_getImagesClosure($generator),
				'title' => self::_getNameClosure($generator),
			]
		);
		$populator->execute();
	}

	private static function _getNameClosure(Generator $generator): \Closure
	{
		return static function () use ($generator): string{
			return $generator->name;
		};
	}

	private static function _getRolesClosure(): \Closure
	{
		return static function () {
			return ['ROLE_USER', 'ROLE_ADMIN'];
		};
	}

	private static function _getPasswordClosure(Generator $generator): \Closure
	{
		return static function () use ($generator) {
			return \password_hash($generator->password, PASSWORD_ARGON2I);
		};
	}

	private static function _getTokenClosure(Generator $generator): \Closure
	{
		return static function () use ($generator) {
			return $generator->word;
		};
	}

	private static function _getImagesClosure(Generator $generator): \Closure
	{
		return static function () use ($generator) {
			$images = [];
			for ($i = $generator->numberBetween(0, 3); $i > 0; --$i) {
				$images[] = $generator->image();
			}

			return $images;
		};
	}

	/**
	 * @param Generator $generator
	 * @return \Closure
	 */
	private static function _getTokenExpirationClosure(Generator $generator): \Closure
	{
		return static function () use ($generator) {
			return $generator->dateTimeBetween('now', '+30 years');
		};
	}
}
