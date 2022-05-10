/**
 * Redacts an email address for privacy reasons.
 *
 * @example
 * redactEmailAddress('hello@blomstra.net'); // 'hel******@blo***.net'
 */
export default function redactEmailAddress(email: string) {
  const [user, domain] = email.split('@');
  const splitDomain = domain.split('.').map((part) => (part.length > 3 ? `${part.substring(0, 3)}***` : part));

  return `${user.substring(0, 3)}******@${splitDomain.join('.')}`;
}
