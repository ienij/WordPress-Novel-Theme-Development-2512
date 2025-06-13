import React from 'react';
import './App.css';

function App() {
  return (
    <div className="min-h-screen bg-white font-sans">
      {/* Header */}
      <header className="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div className="container mx-auto px-4">
          <div className="flex items-center justify-between h-16">
            <div className="flex-shrink-0">
              <a href="#" className="text-2xl font-bold text-black">NovelReader</a>
            </div>
            <nav className="hidden md:flex space-x-8">
              <a href="#" className="text-gray-700 hover:text-black">Home</a>
              <a href="#novels" className="text-gray-700 hover:text-black">Novels</a>
              <a href="#about" className="text-gray-700 hover:text-black">About</a>
            </nav>
            <div className="flex items-center space-x-4">
              <div className="relative">
                <input 
                  type="search" 
                  placeholder="Search novels..." 
                  className="w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-transparent"
                />
                <button className="absolute right-2 top-2 text-gray-400 hover:text-black">
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                  </svg>
                </button>
              </div>
              <a href="#" className="text-gray-700 hover:text-black">Login</a>
              <a href="#" className="bg-black text-white px-4 py-2 rounded hover:bg-gray-800 transition-colors">Sign Up</a>
            </div>
          </div>
        </div>
      </header>

      {/* Hero Section */}
      <section className="bg-black text-white py-20">
        <div className="container mx-auto px-4 text-center">
          <h1 className="text-4xl md:text-6xl font-bold mb-6">
            Discover Amazing Novels
          </h1>
          <p className="text-xl md:text-2xl text-gray-300 mb-8">
            Read the latest translated web novels from around the world
          </p>
          <a href="#novels" className="bg-white text-black px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
            Start Reading
          </a>
        </div>
      </section>

      {/* Featured Novels Section */}
      <section id="novels" className="py-16 bg-gray-50">
        <div className="container mx-auto px-4">
          <h2 className="text-3xl font-bold text-center mb-12">Featured Novels</h2>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {/* Novel Card 1 */}
            <div className="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
              <div className="aspect-[3/4] bg-gray-200 flex items-center justify-center">
                <span className="text-gray-500">Novel Cover</span>
              </div>
              <div className="p-4">
                <h3 className="font-bold text-lg mb-2 line-clamp-2">
                  <a href="#" className="hover:text-gray-600">The Legendary Mechanic</a>
                </h3>
                <p className="text-sm text-gray-600 mb-2">By Qi Peijia</p>
                <span className="inline-block px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full mb-3">
                  Ongoing
                </span>
                <p className="text-gray-600 text-sm mb-4 line-clamp-3">
                  What do you do when you wake up and find yourself inside the very game that you love?
                </p>
                <div className="flex justify-between items-center">
                  <div className="text-xs text-gray-500">1420 chapters</div>
                  <a href="#" className="bg-black text-white px-3 py-1 rounded text-sm hover:bg-gray-800 transition-colors">
                    Read
                  </a>
                </div>
              </div>
            </div>

            {/* Novel Card 2 */}
            <div className="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
              <div className="aspect-[3/4] bg-gray-200 flex items-center justify-center">
                <span className="text-gray-500">Novel Cover</span>
              </div>
              <div className="p-4">
                <h3 className="font-bold text-lg mb-2 line-clamp-2">
                  <a href="#" className="hover:text-gray-600">Reverend Insanity</a>
                </h3>
                <p className="text-sm text-gray-600 mb-2">By Gu Zhen Ren</p>
                <span className="inline-block px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full mb-3">
                  Completed
                </span>
                <p className="text-gray-600 text-sm mb-4 line-clamp-3">
                  Humans are clever in tens of thousands of ways, Gu are the true refined essences of Heaven and Earth.
                </p>
                <div className="flex justify-between items-center">
                  <div className="text-xs text-gray-500">2334 chapters</div>
                  <a href="#" className="bg-black text-white px-3 py-1 rounded text-sm hover:bg-gray-800 transition-colors">
                    Read
                  </a>
                </div>
              </div>
            </div>

            {/* Novel Card 3 */}
            <div className="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
              <div className="aspect-[3/4] bg-gray-200 flex items-center justify-center">
                <span className="text-gray-500">Novel Cover</span>
              </div>
              <div className="p-4">
                <h3 className="font-bold text-lg mb-2 line-clamp-2">
                  <a href="#" className="hover:text-gray-600">Lord of the Mysteries</a>
                </h3>
                <p className="text-sm text-gray-600 mb-2">By Cuttlefish That Loves Diving</p>
                <span className="inline-block px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full mb-3">
                  Completed
                </span>
                <p className="text-gray-600 text-sm mb-4 line-clamp-3">
                  With the rising tide of steam power and machinery, who can come close to being a Beyonder?
                </p>
                <div className="flex justify-between items-center">
                  <div className="text-xs text-gray-500">1394 chapters</div>
                  <a href="#" className="bg-black text-white px-3 py-1 rounded text-sm hover:bg-gray-800 transition-colors">
                    Read
                  </a>
                </div>
              </div>
            </div>

            {/* Novel Card 4 */}
            <div className="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden">
              <div className="aspect-[3/4] bg-gray-200 flex items-center justify-center">
                <span className="text-gray-500">Novel Cover</span>
              </div>
              <div className="p-4">
                <h3 className="font-bold text-lg mb-2 line-clamp-2">
                  <a href="#" className="hover:text-gray-600">Release That Witch</a>
                </h3>
                <p className="text-sm text-gray-600 mb-2">By Er Mu</p>
                <span className="inline-block px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full mb-3">
                  Completed
                </span>
                <p className="text-gray-600 text-sm mb-4 line-clamp-3">
                  Cheng Yan travels through time only to end up becoming an honorable prince in the Middle Ages of Europe.
                </p>
                <div className="flex justify-between items-center">
                  <div className="text-xs text-gray-500">1498 chapters</div>
                  <a href="#" className="bg-black text-white px-3 py-1 rounded text-sm hover:bg-gray-800 transition-colors">
                    Read
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Theme Features Section */}
      <section id="about" className="py-16">
        <div className="container mx-auto px-4">
          <h2 className="text-3xl font-bold text-center mb-12">WordPress Theme Features</h2>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div className="text-center">
              <div className="bg-black text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
              <h3 className="text-xl font-semibold mb-2">Custom Post Types</h3>
              <p className="text-gray-600">Dedicated Novel and Chapter post types with rich metadata support.</p>
            </div>
            
            <div className="text-center">
              <div className="bg-black text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
              </div>
              <h3 className="text-xl font-semibold mb-2">Chapter Locking</h3>
              <p className="text-gray-600">Monetize your content with PayPal integration for premium chapters.</p>
            </div>
            
            <div className="text-center">
              <div className="bg-black text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
              <h3 className="text-xl font-semibold mb-2">Reader Settings</h3>
              <p className="text-gray-600">Customizable reading experience with font controls and themes.</p>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-black text-white py-12">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div className="col-span-2">
              <h3 className="text-xl font-bold mb-4">NovelReader</h3>
              <p className="text-gray-300 mb-4">
                A modern WordPress theme designed specifically for web novel translation sites.
                Features custom post types, reader settings, and monetization options.
              </p>
            </div>
            <div>
              <h4 className="text-lg font-semibold mb-4">Features</h4>
              <ul className="space-y-2">
                <li><span className="text-gray-300">Custom Post Types</span></li>
                <li><span className="text-gray-300">Chapter Locking</span></li>
                <li><span className="text-gray-300">Reader Settings</span></li>
                <li><span className="text-gray-300">PayPal Integration</span></li>
              </ul>
            </div>
            <div>
              <h4 className="text-lg font-semibold mb-4">WordPress</h4>
              <ul className="space-y-2">
                <li><span className="text-gray-300">Clean Code</span></li>
                <li><span className="text-gray-300">Mobile Responsive</span></li>
                <li><span className="text-gray-300">SEO Optimized</span></li>
                <li><span className="text-gray-300">Easy Setup</span></li>
              </ul>
            </div>
          </div>
          
          <div className="border-t border-gray-700 mt-8 pt-8 text-center">
            <p className="text-gray-300 text-sm">
              &copy; 2024 NovelReader WordPress Theme. Modern theme for web novel sites.
            </p>
          </div>
        </div>
      </footer>
    </div>
  );
}

export default App;