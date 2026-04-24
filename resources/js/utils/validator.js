/**
 * A collection of validation helpers for Vue forms
 */

export const useValidators = () => {
  
  // Name: At least 2 characters, alphabetic characters only (including spaces/hyphens)
  const isValidName = (name) => {
    const re = /^[a-zA-Z\s-]{2,50}$/;
    return re.test(name);
  };

  // Email: Standard RFC format
  const isValidEmail = (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
  };

  /**
   * Password: 
   * - At least 8 characters
   * - One uppercase letter
   * - One lowercase letter
   * - One number
   * - One special character
   */
  const isValidPassword = (password) => {
    const re = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    return re.test(password);
  };

  // Date of Birth: Must be 18+ years old
  const isAdult = (dob) => {
    if (!dob) return false;
    const birthDate = new Date(dob);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
      age--;
    }
    return age >= 18;
  };

  // Generic Required Check
  const isRequired = (val) => {
    if (Array.isArray(val)) return val.length > 0;
    return val !== null && val !== undefined && val.toString().trim() !== '';
  };

// Username: 3-20 characters, alphanumeric and hyphens only, must start with letter
const isValidUsername = (username) => {
    if(!username) return false;
    const re = /^[a-zA-Z][a-zA-Z0-9-]{2,19}$/;
    return re.test(username);
};

// Phone Number: Valid international format (10-15 digits, optional +, spaces, hyphens, parentheses)
const isValidPhoneNumber = (phone) => {
    if(!phone) return false;
    const re = /^[+]?[(]?[0-9]{1,4}[)]?[-\s.]?[(]?[0-9]{1,4}[)]?[-\s.]?[0-9]{1,9}$/;
    return re.test(phone.replace(/\s/g, ''));
};

const isAddress = (val) => {
  if (!val) return false;
  
  const trimmed = val.trim();
  
  // 1. Minimum length requirement
  const isLongEnough = trimmed.length >= 5;

  // 2. The "Blocked List" (Dangerous characters)
  // We block: < > { } ; 
  // You can add more to this list if needed (e.g. @ or %)
  const forbiddenChars = /[<>{};]/;
  const hasForbidden = forbiddenChars.test(trimmed);

  // Valid if it's long enough AND has no forbidden characters
  return isLongEnough && !hasForbidden;
};

const isCleansed = (val) => {
  if (!val) return false;
  
  const trimmed = val.trim();
  
  // 1. Minimum length requirement
  const isLongEnough = trimmed.length >= 5;

  // 2. The "Blocked List" (Dangerous characters)
  // We block: < > { } ; 
  // You can add more to this list if needed (e.g. @ or %)
  const forbiddenChars = /[<>{};]/;
  const hasForbidden = forbiddenChars.test(trimmed);

  // Valid if it's long enough AND has no forbidden characters
  return isLongEnough && !hasForbidden;
};



  return {
    isValidName,
    isValidEmail,
    isValidPassword,
    isAdult,
    isRequired,
    isValidUsername,
    isValidPhoneNumber,
    isAddress,
    isCleansed
  };
};