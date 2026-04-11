<template>
  <div class="max-w-7xl mx-auto mt-4">
    <div class="flex flex-wrap -mx-2">
      <div class="w-full lg:w-2/3 px-2">
        <div class="bg-white rounded-lg shadow-md mb-4">
          <div class="bg-blue-600 px-4 py-3 rounded-t-lg">
            <h6 class="m-0 font-bold text-white">Dashboard Test</h6>
          </div>
          <div class="p-4">
            <p>Đây là trang test master layout</p>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
              Master layout đang hoạt động bình thường! ✓
            </div>
            <h5>Các thành phần đã được tách:</h5>
            <ul class="list-disc list-inside">
              <li><strong>Sidebar.vue</strong> - Menu bên trái</li>
              <li><strong>Header.vue</strong> - Thanh công cụ trên cùng</li>
              <li><strong>Footer.vue</strong> - Chân trang</li>
              <li><strong>MasterLayout.vue</strong> - Bố cục chính</li>
              <li><strong>layout.css</strong> - CSS chung</li>
            </ul>
            
            <h5 class="mt-4">Cách sử dụng:</h5>
            <code class="block bg-gray-100 p-2 rounded">
              &lt;MasterLayout&gt;<br>
              &nbsp;&nbsp;&lt;!-- Nội dung trang của bạn --&gt;<br>
              &lt;/MasterLayout&gt;
            </code>
          </div>
        </div>
      </div>
      <div class="w-full lg:w-1/3 px-2">
        <div class="bg-white rounded-lg shadow-md mb-4">
          <div class="bg-blue-400 px-4 py-3 rounded-t-lg">
            <h6 class="m-0 font-bold text-white">Thông tin</h6>
          </div>
          <div class="p-4">
            <p><strong>Trạng thái:</strong> <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm">Hoạt động</span></p>
            <p><strong>Route hiện tại:</strong> {{ $route.path }}</p>
            <p><strong>Tên route:</strong> {{ $route.name || 'dashboard' }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DashboardTest',
};
</script>

<style scoped>
.container-fluid {
  background-color: #f8f9fc;
  min-height: 100%;
}

.card {
  border: none;
  border-radius: 0.35rem;
  border-left: 0.25rem solid #224abe;
}

.card-header {
  background: linear-gradient(135deg, #224abe 0%, #00b4d8 100%);
  border-bottom: none;
  padding: 1.5rem;
}

.card-header.bg-primary {
  background: linear-gradient(135deg, #224abe 0%, #00b4d8 100%);
}

.card-header.bg-info {
  background: linear-gradient(135deg, #00b4d8 0%, #0096c7 100%);
}

.card-body {
  padding: 1.5rem;
}

.text-white {
  color: white;
}

.m-0 {
  margin: 0;
}

.mt-4 {
  margin-top: 1.5rem;
}

.mb-4 {
  margin-bottom: 1.5rem;
}

.alert {
  padding: 1rem;
  border-radius: 0.35rem;
  border: none;
}

.alert-success {
  background-color: #d4edda;
  color: #155724;
  border-left: 4px solid #1cc88a;
}

.badge {
  padding: 0.5rem 1rem;
  border-radius: 0.25rem;
  font-weight: 600;
  display: inline-block;
}

.badge-success {
  background-color: #1cc88a;
  color: white;
}

code {
  background-color: #f8f9fc;
  border: 1px solid #e3e6f0;
  padding: 1rem;
  border-radius: 0.35rem;
  display: block;
  color: #333;
  font-family: 'Courier New', monospace;
  overflow-x: auto;
}

ul {
  padding-left: 1.5rem;
}

ul li {
  margin-bottom: 0.5rem;
}

h5 {
  color: #333;
  font-weight: 600;
  margin-bottom: 1rem;
}

p {
  margin-bottom: 0.5rem;
  color: #858796;
}
</style>
