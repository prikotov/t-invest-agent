<?php

declare(strict_types=1);

namespace TInvestAgentCodingStandard\Sniffs\Structure;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

final class DtoStructureSniff implements Sniff
{
    private const ERROR_FINAL_READONLY_REQUIRED = 'FinalReadonlyRequired';
    private const ERROR_CONSTRUCTOR_REQUIRED = 'ConstructorRequired';
    private const ERROR_CONSTRUCTOR_NOT_EMPTY = 'ConstructorMustBeEmpty';
    private const ERROR_FORBIDDEN_MEMBERS = 'ForbiddenMembers';

    public function register(): array
    {
        return [T_CLASS];
    }

    /**
     * @param int $stackPtr Pointer to the class token.
     */
    public function process(File $phpcsFile, $stackPtr): void
    {
        $className = $phpcsFile->getDeclarationName($stackPtr);
        if ($className === '' || str_ends_with($className, 'Dto') === false) {
            return;
        }

        if ($this->isDtoPath($phpcsFile->getFilename()) === false) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]['scope_opener'], $tokens[$stackPtr]['scope_closer']) === false) {
            return;
        }

        $this->assertFinalReadonly($phpcsFile, $stackPtr);

        $scopeStart = $tokens[$stackPtr]['scope_opener'];
        $scopeEnd   = $tokens[$stackPtr]['scope_closer'];

        $constructorPtr = $this->assertOnlyConstructor($phpcsFile, $stackPtr, $scopeStart, $scopeEnd);

        if ($constructorPtr !== null) {
            $this->assertConstructorIsEmpty($phpcsFile, $constructorPtr);
        }

        $this->assertNoMembers($phpcsFile, $stackPtr, $scopeStart, $scopeEnd);
    }

    private function isDtoPath(string $filename): bool
    {
        $normalizedPath = str_replace('\\', '/', $filename);

        return str_contains($normalizedPath, '/Component/')
            || str_contains($normalizedPath, '/Service/');
    }

    private function assertFinalReadonly(File $phpcsFile, int $classPtr): void
    {
        $properties = $phpcsFile->getClassProperties($classPtr);
        if (($properties['is_final'] ?? false) === false || ($properties['is_readonly'] ?? false) === false) {
            $phpcsFile->addError(
                'DTO classes must be declared as final readonly.',
                $classPtr,
                self::ERROR_FINAL_READONLY_REQUIRED,
            );
        }
    }

    private function assertOnlyConstructor(File $phpcsFile, int $classPtr, int $scopeStart, int $scopeEnd): ?int
    {
        $constructorPtr = null;
        $tokens         = $phpcsFile->getTokens();
        $pointer        = $scopeStart;

        while (($pointer = $phpcsFile->findNext(T_FUNCTION, $pointer + 1, $scopeEnd)) !== false) {
            if ($this->belongsToClass($tokens, $pointer, $classPtr) === false) {
                continue;
            }

            $methodName = strtolower($phpcsFile->getDeclarationName($pointer));
            if ($methodName !== '__construct') {
                $phpcsFile->addError(
                    'DTO classes must not declare methods other than the constructor.',
                    $pointer,
                    self::ERROR_FORBIDDEN_MEMBERS,
                );
                continue;
            }

            $constructorPtr = $pointer;
        }

        if ($constructorPtr === null) {
            $phpcsFile->addError(
                'DTO classes must declare a constructor with promoted readonly properties.',
                $classPtr,
                self::ERROR_CONSTRUCTOR_REQUIRED,
            );
        }

        return $constructorPtr;
    }

    private function assertConstructorIsEmpty(File $phpcsFile, int $constructorPtr): void
    {
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$constructorPtr]['scope_opener'], $tokens[$constructorPtr]['scope_closer']) === false) {
            return;
        }

        $bodyStart = $tokens[$constructorPtr]['scope_opener'];
        $bodyEnd   = $tokens[$constructorPtr]['scope_closer'];

        $nextToken = $phpcsFile->findNext(
            Tokens::$emptyTokens,
            $bodyStart + 1,
            $bodyEnd,
            true,
        );

        if ($nextToken !== false) {
            $phpcsFile->addError(
                'Constructor of DTO classes must not contain executable code.',
                $nextToken,
                self::ERROR_CONSTRUCTOR_NOT_EMPTY,
            );
        }
    }

    private function assertNoMembers(File $phpcsFile, int $classPtr, int $scopeStart, int $scopeEnd): void
    {
        $tokens  = $phpcsFile->getTokens();
        $pointer = $scopeStart;

        while (($pointer = $phpcsFile->findNext([T_VARIABLE], $pointer + 1, $scopeEnd)) !== false) {
            if ($this->belongsToClass($tokens, $pointer, $classPtr) === false) {
                continue;
            }

            try {
                $member = $phpcsFile->getMemberProperties($pointer);
            } catch (RuntimeException $exception) {
                continue;
            }

            if ($member === []) {
                continue;
            }

            $phpcsFile->addError(
                'DTO classes must not declare properties; use promoted readonly constructor parameters instead.',
                $pointer,
                self::ERROR_FORBIDDEN_MEMBERS,
            );
        }

        $this->assertNoTokens($phpcsFile, $classPtr, $scopeStart, $scopeEnd, [T_CONST], 'DTO classes must not declare constants.');
        $this->assertNoTokens($phpcsFile, $classPtr, $scopeStart, $scopeEnd, [T_USE], 'DTO classes must not use traits.');
    }

    /**
     * @param array<int, array<string, mixed>> $tokens
     */
    private function belongsToClass(array $tokens, int $tokenPtr, int $classPtr): bool
    {
        if (isset($tokens[$tokenPtr]['conditions']) === false || $tokens[$tokenPtr]['conditions'] === []) {
            return false;
        }

        return array_key_last($tokens[$tokenPtr]['conditions']) === $classPtr;
    }

    private function assertNoTokens(
        File $phpcsFile,
        int $classPtr,
        int $scopeStart,
        int $scopeEnd,
        array $tokenTypes,
        string $message,
    ): void {
        $tokens  = $phpcsFile->getTokens();
        $pointer = $scopeStart;

        while (($pointer = $phpcsFile->findNext($tokenTypes, $pointer + 1, $scopeEnd)) !== false) {
            if ($this->belongsToClass($tokens, $pointer, $classPtr) === false) {
                continue;
            }

            $phpcsFile->addError(
                $message,
                $pointer,
                self::ERROR_FORBIDDEN_MEMBERS,
            );
        }
    }
}
